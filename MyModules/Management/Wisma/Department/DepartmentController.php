<?php
declare(strict_types=1);

/**
 * DepartmentController: the logic of the Department page (Department.php).
 *
 * Department.php calls handleDepartment() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination and
 *     the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a department was added
 *   UPDATED        a department was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate code / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate code / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/DepartmentModel.php';

const DEPARTMENT_TABLE         = 'ref_department';
const DEPARTMENT_CODE_MAX      = 50;
const DEPARTMENT_NAME_MAX      = 255;
const DEPARTMENT_PER_PAGE      = 10;

if (!function_exists('departmentListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function departmentListQuery(array $source): array
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

if (!function_exists('departmentBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function departmentBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['department_old'] = $old;
        }
        redirect(routeUrl('wisma-department', departmentListQuery($_POST)), 303);
    }
}

if (!function_exists('departmentFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, code, description, status)
     */
    function departmentFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, DEPARTMENT_TABLE, $id, null, [
            'reason'      => $message,
            'code'        => $old['code'],
            'description' => $old['description'],
            'status'      => $old['status'],
        ]);
        departmentBack('error', $message, $old);
    }
}

if (!function_exists('departmentSave')) {
    /** Handle POST action=create and action=update. */
    function departmentSave(string $action): never
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
            $current = $id > 0 ? DepartmentModel::findById($id) : null;
            if ($current === null) {
                departmentFail($failed, $id, 'Rekod jabatan tidak ditemui.', $old);
            }
        }
        if ($code === '' || $description === '') {
            departmentFail($failed, $id, 'Sila isi Kod Jabatan dan Nama Jabatan.', $old);
        }
        if (mb_strlen($code) > DEPARTMENT_CODE_MAX) {
            departmentFail($failed, $id, 'Kod Jabatan terlalu panjang (maksimum ' . DEPARTMENT_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > DEPARTMENT_NAME_MAX) {
            departmentFail($failed, $id, 'Nama Jabatan terlalu panjang (maksimum ' . DEPARTMENT_NAME_MAX . ' aksara).', $old);
        }
        if (!DepartmentModel::isValidStatus($statusId)) {
            departmentFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (DepartmentModel::codeExists($code, $id)) {
            departmentFail($failed, $id, "Kod Jabatan {$code} sudah wujud.", $old);
        }

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'ref_department_code'        => $code,
            'ref_department_description' => $description,
            'ref_status_id'              => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = DepartmentModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', DEPARTMENT_TABLE, $id, null, $new);
                    departmentBack('info', 'Tiada perubahan dibuat.');
                }

                DepartmentModel::update($id, $code, $description, $statusId, $userId);
                audit('UPDATED', DEPARTMENT_TABLE, $id, $before, $new);
                departmentBack('success', "Jabatan {$code} berjaya dikemas kini.");
            }

            $newId = DepartmentModel::create($code, $description, $statusId, $userId);
            audit('CREATED', DEPARTMENT_TABLE, $newId, null, $new);
            departmentBack('success', "Jabatan {$code} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[DEPARTMENT] ' . $e->getMessage());
            departmentFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleDepartment')) {
    /**
     * Run the Department page logic.
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
    function handleDepartment(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', DEPARTMENT_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                departmentBack('error', 'Tindakan tidak sah.');
            }
            departmentSave($action);
        }

        // ---------- Normal page view ----------
        $query  = departmentListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(DepartmentModel::count($q, $status), currentPage(), DEPARTMENT_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['department_old'] ?? null;
        unset($_SESSION['department_old']);

        return [
            'rows'          => DepartmentModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => DepartmentModel::stats(),
            'page'          => $page,
            'statusOptions' => DepartmentModel::statusOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
