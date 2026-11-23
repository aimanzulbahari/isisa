<?php
declare(strict_types=1);

/**
 * RoleTypeController: the logic of the RoleType page (Role-Type.php).
 *
 * Role-Type.php calls handleRoleType() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination and
 *     the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a roleType was added
 *   UPDATED        a roleType was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate code / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate code / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/RoleTypeModel.php';

const ROLE_TYPE_TABLE         = 'ref_role_type';
const ROLE_TYPE_CODE_MAX      = 50;
const ROLE_TYPE_NAME_MAX      = 255;
const ROLE_TYPE_PER_PAGE      = 10;

if (!function_exists('roleTypeListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function roleTypeListQuery(array $source): array
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

if (!function_exists('roleTypeBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function roleTypeBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['role_type_old'] = $old;
        }
        redirect(routeUrl('role-type', roleTypeListQuery($_POST)), 303);
    }
}

if (!function_exists('roleTypeFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, code, description, status)
     */
    function roleTypeFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, ROLE_TYPE_TABLE, $id, null, [
            'reason'      => $message,
            'code'        => $old['code'],
            'description' => $old['description'],
            'status'      => $old['status'],
        ]);
        roleTypeBack('error', $message, $old);
    }
}

if (!function_exists('roleTypeSave')) {
    /** Handle POST action=create and action=update. */
    function roleTypeSave(string $action): never
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
            $current = $id > 0 ? RoleTypeModel::findById($id) : null;
            if ($current === null) {
                roleTypeFail($failed, $id, 'Rekod jenis peranan tidak ditemui.', $old);
            }
        }
        if ($code === '' || $description === '') {
            roleTypeFail($failed, $id, 'Sila isi Kod dan Nama.', $old);
        }
        if (mb_strlen($code) > ROLE_TYPE_CODE_MAX) {
            roleTypeFail($failed, $id, 'Kod Jenis Peranan terlalu panjang (maksimum ' . ROLE_TYPE_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > ROLE_TYPE_NAME_MAX) {
            roleTypeFail($failed, $id, 'Nama Jenis Peranan terlalu panjang (maksimum ' . ROLE_TYPE_NAME_MAX . ' aksara).', $old);
        }
        if (!preg_match('/^[A-Z0-9_]+$/', $code)) {
            roleTypeFail($failed, $id, 'Kod hanya boleh mengandungi huruf besar, nombor dan garis bawah (_).', $old);
        }
        if (!RoleTypeModel::isValidStatus($statusId)) {
            roleTypeFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (RoleTypeModel::codeExists($code, $id)) {
            roleTypeFail($failed, $id, "Kod Jenis Peranan {$code} sudah wujud.", $old);
        }

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'ref_role_type_code'        => $code,
            'ref_role_type_description' => $description,
            'ref_status_id'              => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = RoleTypeModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', ROLE_TYPE_TABLE, $id, null, $new);
                    roleTypeBack('info', 'Tiada perubahan dibuat.');
                }

                RoleTypeModel::update($id, $code, $description, $statusId, $userId);
                audit('UPDATED', ROLE_TYPE_TABLE, $id, $before, $new);
                roleTypeBack('success', "Jenis Peranan {$code} berjaya dikemas kini.");
            }

            $newId = RoleTypeModel::create($code, $description, $statusId, $userId);
            audit('CREATED', ROLE_TYPE_TABLE, $newId, null, $new);
            roleTypeBack('success', "Jenis Peranan {$code} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[ROLE_TYPE] ' . $e->getMessage());
            roleTypeFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleRoleType')) {
    /**
     * Run the RoleType page logic.
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
    function handleRoleType(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', ROLE_TYPE_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                roleTypeBack('error', 'Tindakan tidak sah.');
            }
            roleTypeSave($action);
        }

        // ---------- Normal page view ----------
        $query  = roleTypeListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(RoleTypeModel::count($q, $status), currentPage(), ROLE_TYPE_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['role_type_old'] ?? null;
        unset($_SESSION['role_type_old']);

        return [
            'rows'          => RoleTypeModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => RoleTypeModel::stats(),
            'page'          => $page,
            'statusOptions' => RoleTypeModel::statusOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
