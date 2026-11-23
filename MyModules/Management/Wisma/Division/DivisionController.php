<?php
declare(strict_types=1);

/**
 * DivisionController: the logic of the Division page (Division.php).
 *
 * Division.php calls handleDivision() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination and
 *     the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a division was added
 *   UPDATED        a division was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate code / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate code / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/DivisionModel.php';

const DIVISION_TABLE         = 'ref_division';
const DIVISION_CODE_MAX      = 50;
const DIVISION_NAME_MAX      = 255;
const DIVISION_PER_PAGE      = 10;

if (!function_exists('divisionListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function divisionListQuery(array $source): array
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

if (!function_exists('divisionBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function divisionBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['division_old'] = $old;
        }
        redirect(routeUrl('wisma-division', divisionListQuery($_POST)), 303);
    }
}

if (!function_exists('divisionFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, code, description, status)
     */
    function divisionFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, DIVISION_TABLE, $id, null, [
            'reason'      => $message,
            'code'        => $old['code'],
            'description' => $old['description'],
            'status'      => $old['status'],
        ]);
        divisionBack('error', $message, $old);
    }
}

if (!function_exists('divisionSave')) {
    /** Handle POST action=create and action=update. */
    function divisionSave(string $action): never
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
            $current = $id > 0 ? DivisionModel::findById($id) : null;
            if ($current === null) {
                divisionFail($failed, $id, 'Rekod bahagian tidak ditemui.', $old);
            }
        }
        if ($code === '' || $description === '') {
            divisionFail($failed, $id, 'Sila isi Kod Bahagian dan Nama Bahagian.', $old);
        }
        if (mb_strlen($code) > DIVISION_CODE_MAX) {
            divisionFail($failed, $id, 'Kod Bahagian terlalu panjang (maksimum ' . DIVISION_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > DIVISION_NAME_MAX) {
            divisionFail($failed, $id, 'Nama Bahagian terlalu panjang (maksimum ' . DIVISION_NAME_MAX . ' aksara).', $old);
        }
        if (!DivisionModel::isValidStatus($statusId)) {
            divisionFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (DivisionModel::codeExists($code, $id)) {
            divisionFail($failed, $id, "Kod Bahagian {$code} sudah wujud.", $old);
        }

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'ref_division_code'        => $code,
            'ref_division_description' => $description,
            'ref_status_id'              => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = DivisionModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', DIVISION_TABLE, $id, null, $new);
                    divisionBack('info', 'Tiada perubahan dibuat.');
                }

                DivisionModel::update($id, $code, $description, $statusId, $userId);
                audit('UPDATED', DIVISION_TABLE, $id, $before, $new);
                divisionBack('success', "Bahagian {$code} berjaya dikemas kini.");
            }

            $newId = DivisionModel::create($code, $description, $statusId, $userId);
            audit('CREATED', DIVISION_TABLE, $newId, null, $new);
            divisionBack('success', "Bahagian {$code} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[DIVISION] ' . $e->getMessage());
            divisionFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleDivision')) {
    /**
     * Run the Division page logic.
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
    function handleDivision(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', DIVISION_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                divisionBack('error', 'Tindakan tidak sah.');
            }
            divisionSave($action);
        }

        // ---------- Normal page view ----------
        $query  = divisionListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(DivisionModel::count($q, $status), currentPage(), DIVISION_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['division_old'] ?? null;
        unset($_SESSION['division_old']);

        return [
            'rows'          => DivisionModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => DivisionModel::stats(),
            'page'          => $page,
            'statusOptions' => DivisionModel::statusOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
