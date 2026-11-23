<?php
declare(strict_types=1);

/**
 * RoleController: the logic of the Role page (Role.php).
 *
 * Role.php calls handleRole() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination and
 *     the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a role was added
 *   UPDATED        a role was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate code / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate code / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/RoleModel.php';

const ROLE_TABLE         = 'ref_role';
const ROLE_CODE_MAX      = 50;
const ROLE_NAME_MAX      = 255;
const ROLE_PER_PAGE      = 10;

if (!function_exists('roleListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function roleListQuery(array $source): array
    {
        $query = [];

        $q = trim((string) ($source['q'] ?? ''));
        if ($q !== '') {
            $query['q'] = mb_substr($q, 0, 100);
        }
        $status = toInt($source['status'] ?? 0);
        if ($status > 0) {
            $query['status'] = $status;
        }
        $page = toInt($source['page'] ?? 1, 1);
        if ($page > 1) {
            $query['page'] = $page;
        }
        return $query;
    }
}

if (!function_exists('roleBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function roleBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['role_old'] = $old;
        }
        redirect(routeUrl('role-role', roleListQuery($_POST)), 303);
    }
}

if (!function_exists('roleFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, code, description, status)
     */
    function roleFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, ROLE_TABLE, $id, null, [
            'reason'      => $message,
            'code'        => $old['code'],
            'description' => $old['description'],
            'status'      => $old['status'],
        ]);
        roleBack('error', $message, $old);
    }
}

if (!function_exists('roleSave')) {
    /** Handle POST action=create and action=update. */
    function roleSave(string $action): never
    {
        $isEdit = $action === 'update';
        $failed = $isEdit ? 'UPDATE_FAILED' : 'CREATE_FAILED';

        $id = $isEdit ? toInt($_POST['id'] ?? 0) : 0;

        // Uppercase the code, and collapse the spaces in the name
        $code        = mb_strtoupper(trim((string) ($_POST['code'] ?? '')));
        $description = trim((string) preg_replace('/\s+/u', ' ', (string) ($_POST['description'] ?? '')));
        $statusId    = toInt($_POST['status_id'] ?? 0);

        $old = [
            'action'      => $action,
            'id'          => $id,
            'code'        => $code,
            'description' => $description,
            'status'      => $statusId,
        ];

        // 1. Input check
        $current = null;
        if ($isEdit) {
            $current = $id > 0 ? RoleModel::findById($id) : null;
            if ($current === null) {
                roleFail($failed, $id, 'Rekod peranan tidak ditemui.', $old);
            }
        }
        if ($code === '' || $description === '') {
            roleFail($failed, $id, 'Sila isi Kod dan Nama.', $old);
        }
        if (mb_strlen($code) > ROLE_CODE_MAX) {
            roleFail($failed, $id, 'Kod Peranan terlalu panjang (maksimum ' . ROLE_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > ROLE_NAME_MAX) {
            roleFail($failed, $id, 'Nama Peranan terlalu panjang (maksimum ' . ROLE_NAME_MAX . ' aksara).', $old);
        }
        if (!preg_match('/^[A-Z0-9_]+$/', $code)) {
            roleFail($failed, $id, 'Kod hanya boleh mengandungi huruf besar, nombor dan garis bawah (_).', $old);
        }
        // System roles are used by the access checks (requireAdmin etc.): keep their code and keep them active
        if ($isEdit && in_array($current['ref_role_code'], ['SUPERADMIN', 'ADMIN'], true)
            && ($code !== $current['ref_role_code'] || $statusId !== (int) config('app.status')['ACTIVE'])) {
            roleFail($failed, $id, 'Peranan sistem tidak boleh ditukar kod atau dinyahaktifkan.', $old);
        }
        if (!RoleModel::isValidStatus($statusId)) {
            roleFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (RoleModel::codeExists($code, $id)) {
            roleFail($failed, $id, "Kod Peranan {$code} sudah wujud.", $old);
        }

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'ref_role_code'        => $code,
            'ref_role_description' => $description,
            'ref_status_id'              => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = RoleModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', ROLE_TABLE, $id, null, $new);
                    roleBack('info', 'Tiada perubahan dibuat.');
                }

                RoleModel::update($id, $code, $description, $statusId, $userId);
                audit('UPDATED', ROLE_TABLE, $id, $before, $new);
                roleBack('success', "Peranan {$code} berjaya dikemas kini.");
            }

            $newId = RoleModel::create($code, $description, $statusId, $userId);
            audit('CREATED', ROLE_TABLE, $newId, null, $new);
            roleBack('success', "Peranan {$code} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[ROLE] ' . $e->getMessage());
            roleFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleRole')) {
    /**
     * Run the Role page logic.
     *
     * @return array{
     *   rows: array<int,array<string,mixed>>,
     *   stats: array{total:int,active:int,not_active:int,last_updated:?string},
     *   page: array<string,mixed>,
     *   statusOptions: array<int,array<string,mixed>>,
     *   q: string,
     *   status: int,
     *   old: ?array<string,mixed>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleRole(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', ROLE_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                roleBack('error', 'Tindakan tidak sah.');
            }
            roleSave($action);
        }

        // ---------- Normal page view ----------
        $query  = roleListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(RoleModel::count($q, $status), currentPage(), ROLE_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['role_old'] ?? null;
        unset($_SESSION['role_old']);

        return [
            'rows'          => RoleModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => RoleModel::stats(),
            'page'          => $page,
            'statusOptions' => RoleModel::statusOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
