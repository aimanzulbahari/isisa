<?php
declare(strict_types=1);

/**
 * BranchController: the logic of the Branch page (Branch.php).
 *
 * Branch.php calls handleBranch() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination and
 *     the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a branch was added
 *   UPDATED        a branch was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate code / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate code / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/BranchModel.php';

const BRANCH_TABLE         = 'ref_branch';
const BRANCH_CODE_MAX      = 50;
const BRANCH_NAME_MAX      = 255;
const BRANCH_PER_PAGE      = 10;

if (!function_exists('branchListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function branchListQuery(array $source): array
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

if (!function_exists('branchBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function branchBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['branch_old'] = $old;
        }
        redirect(routeUrl('wisma-branch', branchListQuery($_POST)), 303);
    }
}

if (!function_exists('branchFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, code, description, status)
     */
    function branchFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, BRANCH_TABLE, $id, null, [
            'reason'      => $message,
            'code'        => $old['code'],
            'description' => $old['description'],
            'status'      => $old['status'],
        ]);
        branchBack('error', $message, $old);
    }
}

if (!function_exists('branchSave')) {
    /** Handle POST action=create and action=update. */
    function branchSave(string $action): never
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
            $current = $id > 0 ? BranchModel::findById($id) : null;
            if ($current === null) {
                branchFail($failed, $id, 'Rekod cawangan tidak ditemui.', $old);
            }
        }
        if ($code === '' || $description === '') {
            branchFail($failed, $id, 'Sila isi Kod Cawangan dan Nama Cawangan.', $old);
        }
        if (mb_strlen($code) > BRANCH_CODE_MAX) {
            branchFail($failed, $id, 'Kod Cawangan terlalu panjang (maksimum ' . BRANCH_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > BRANCH_NAME_MAX) {
            branchFail($failed, $id, 'Nama Cawangan terlalu panjang (maksimum ' . BRANCH_NAME_MAX . ' aksara).', $old);
        }
        if (!BranchModel::isValidStatus($statusId)) {
            branchFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (BranchModel::codeExists($code, $id)) {
            branchFail($failed, $id, "Kod Cawangan {$code} sudah wujud.", $old);
        }

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'ref_branch_code'        => $code,
            'ref_branch_description' => $description,
            'ref_status_id'              => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = BranchModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', BRANCH_TABLE, $id, null, $new);
                    branchBack('info', 'Tiada perubahan dibuat.');
                }

                BranchModel::update($id, $code, $description, $statusId, $userId);
                audit('UPDATED', BRANCH_TABLE, $id, $before, $new);
                branchBack('success', "Cawangan {$code} berjaya dikemas kini.");
            }

            $newId = BranchModel::create($code, $description, $statusId, $userId);
            audit('CREATED', BRANCH_TABLE, $newId, null, $new);
            branchBack('success', "Cawangan {$code} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[BRANCH] ' . $e->getMessage());
            branchFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleBranch')) {
    /**
     * Run the Branch page logic.
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
    function handleBranch(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', BRANCH_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                branchBack('error', 'Tindakan tidak sah.');
            }
            branchSave($action);
        }

        // ---------- Normal page view ----------
        $query  = branchListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(BranchModel::count($q, $status), currentPage(), BRANCH_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['branch_old'] ?? null;
        unset($_SESSION['branch_old']);

        return [
            'rows'          => BranchModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => BranchModel::stats(),
            'page'          => $page,
            'statusOptions' => BranchModel::statusOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
