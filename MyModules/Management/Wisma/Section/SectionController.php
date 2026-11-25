<?php
declare(strict_types=1);

/**
 * SectionController: the logic of the Section page (Section.php).
 *
 * Section.php calls handleSection() once. The function:
 *   - on a normal page view (GET): returns the sections GROUPED BY DEPARTMENT, the statistics,
 *     the dropdown choices and the messages to show. Search / filter come from the URL
 *     (?q=...&status=...), so everything is done on the server. The page shows one accordion
 *     per department; it opens every group while a search / filter is active, and the group
 *     named by ?open=<department id> (the department of the section that was just saved).
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Rules:
 *   - Jabatan is required and must be an active department (an edit may keep its old one).
 *   - Kod Seksyen is required, uppercase, and unique in the whole table.
 *   - Nama Seksyen is required and unique inside its department.
 *   - Keterangan is optional (max 300 characters).
 *   - A section that has employees cannot be moved to another department, because the
 *     employees' own department would no longer match.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        a section was added
 *   UPDATED        a section was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 */

require_once __DIR__ . '/SectionModel.php';

const SECTION_TABLE    = 'ref_section';
const SECTION_CODE_MAX = 50;    // ref_section_code VARCHAR(50)
const SECTION_NAME_MAX = 255;   // ref_section_name VARCHAR(255)
const SECTION_DESC_MAX = 300;   // ref_section_description VARCHAR(300)

if (!function_exists('sectionListQuery')) {
    /**
     * The list filters (q, status, open) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function sectionListQuery(array $source): array
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
        $open = toInt($source['open'] ?? 0);
        if ($open > 0) {
            $query['open'] = $open;
        }
        return $query;
    }
}

if (!function_exists('sectionBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old      Typed values, to re-open the form with them
     * @param int                      $openDept Department group to open after the redirect (0 = none)
     */
    function sectionBack(string $type, string $message, ?array $old = null, int $openDept = 0): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['section_old'] = $old;
        }

        $query = sectionListQuery($_POST);
        unset($query['open']);
        if ($openDept > 0) {
            $query['open'] = $openDept;
        }
        redirect(routeUrl('wisma-section', $query), 303);
    }
}

if (!function_exists('sectionFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (action, id, department_id, code, name, description, status)
     */
    function sectionFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, SECTION_TABLE, $id, null, [
            'reason'        => $message,
            'department_id' => $old['department_id'],
            'code'          => $old['code'],
            'name'          => $old['name'],
            'description'   => $old['description'],
            'status'        => $old['status'],
        ]);
        sectionBack('error', $message, $old, (int) $old['department_id']);
    }
}

if (!function_exists('sectionSave')) {
    /** Handle POST action=create and action=update. */
    function sectionSave(string $action): never
    {
        $isEdit = $action === 'update';
        $failed = $isEdit ? 'UPDATE_FAILED' : 'CREATE_FAILED';

        $id = $isEdit ? toInt($_POST['id'] ?? 0) : 0;

        // Uppercase the code, collapse the spaces in the name; the description keeps its line breaks
        $departmentId = toInt($_POST['department_id'] ?? 0);
        $code         = mb_strtoupper(trim((string) ($_POST['code'] ?? '')));
        $name         = trim((string) preg_replace('/\s+/u', ' ', (string) ($_POST['name'] ?? '')));
        $description  = trim(str_replace("\r\n", "\n", (string) ($_POST['description'] ?? '')));
        $statusId     = toInt($_POST['status_id'] ?? 0);

        $old = [
            'action'        => $action,
            'id'            => $id,
            'department_id' => $departmentId,
            'code'          => $code,
            'name'          => $name,
            'description'   => $description,
            'status'        => $statusId,
        ];

        // 1. Input check
        $current = null;
        if ($isEdit) {
            $current = $id > 0 ? SectionModel::findById($id) : null;
            if ($current === null) {
                sectionFail($failed, $id, 'Rekod seksyen tidak ditemui.', $old);
            }
        }

        // An edit may keep a department that has since become inactive; adding / moving needs an active one
        $keepsDepartment = $current !== null && (int) $current['ref_department_id'] === $departmentId;
        if ($departmentId <= 0 || (!$keepsDepartment && !SectionModel::departmentIsActive($departmentId))) {
            sectionFail($failed, $id, 'Sila pilih Jabatan yang sah.', $old);
        }
        if ($code === '' || $name === '') {
            sectionFail($failed, $id, 'Sila isi Kod Seksyen dan Nama Seksyen.', $old);
        }
        if (mb_strlen($code) > SECTION_CODE_MAX) {
            sectionFail($failed, $id, 'Kod Seksyen terlalu panjang (maksimum ' . SECTION_CODE_MAX . ' aksara).', $old);
        }
        if (mb_strlen($name) > SECTION_NAME_MAX) {
            sectionFail($failed, $id, 'Nama Seksyen terlalu panjang (maksimum ' . SECTION_NAME_MAX . ' aksara).', $old);
        }
        if (mb_strlen($description) > SECTION_DESC_MAX) {
            sectionFail($failed, $id, 'Keterangan terlalu panjang (maksimum ' . SECTION_DESC_MAX . ' aksara).', $old);
        }
        if (!SectionModel::isValidStatus($statusId)) {
            sectionFail($failed, $id, 'Status tidak sah.', $old);
        }
        if (SectionModel::codeExists($code, $id)) {
            sectionFail($failed, $id, "Kod Seksyen {$code} sudah wujud.", $old);
        }
        if (SectionModel::nameExistsInDepartment($name, $departmentId, $id)) {
            sectionFail($failed, $id, "Jabatan ini sudah mempunyai seksyen bernama \"{$name}\".", $old);
        }
        if ($current !== null && !$keepsDepartment) {
            $employees = SectionModel::employeeCount($id);
            if ($employees > 0) {
                sectionFail(
                    $failed,
                    $id,
                    "Seksyen ini mempunyai {$employees} kakitangan, jadi Jabatan tidak boleh ditukar.",
                    $old
                );
            }
        }

        // 2. Save + audit
        $userId = currentUserId();
        $text   = $description === '' ? null : $description;
        $new    = [
            'ref_department_id'       => $departmentId,
            'ref_section_code'        => $code,
            'ref_section_name'        => $name,
            'ref_section_description' => $text,
            'ref_status_id'           => $statusId,
        ];

        try {
            if ($isEdit) {
                $before = SectionModel::auditSnapshot($current);

                // Nothing changed: keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === []) {
                    audit('NO_CHANGE', SECTION_TABLE, $id, null, $new);
                    sectionBack('info', 'Tiada perubahan dibuat.', null, $departmentId);
                }

                SectionModel::update($id, $departmentId, $code, $name, $text, $statusId, $userId);
                audit('UPDATED', SECTION_TABLE, $id, $before, $new);
                sectionBack('success', "Seksyen {$code} berjaya dikemas kini.", null, $departmentId);
            }

            $newId = SectionModel::create($departmentId, $code, $name, $text, $statusId, $userId);
            audit('CREATED', SECTION_TABLE, $newId, null, $new);
            sectionBack('success', "Seksyen {$code} berjaya ditambah.", null, $departmentId);
        } catch (PDOException $e) {
            error_log('[SECTION] ' . $e->getMessage());
            sectionFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('sectionGroups')) {
    /**
     * Group the rows by department, keeping the order of the query.
     *
     * @param  array<int,array<string,mixed>> $rows
     * @return array<int,array{id:int,code:string,name:string,sections:array<int,array<string,mixed>>}>
     */
    function sectionGroups(array $rows): array
    {
        $groups = [];
        foreach ($rows as $row) {
            $deptId = (int) $row['ref_department_id'];
            $groups[$deptId] ??= [
                'id'       => $deptId,
                'code'     => (string) $row['ref_department_code'],
                'name'     => (string) $row['ref_department_description'],
                'sections' => [],
            ];
            $groups[$deptId]['sections'][] = $row;
        }
        return array_values($groups);
    }
}

if (!function_exists('handleSection')) {
    /**
     * Run the Section page logic.
     *
     * @return array{
     *   groups: array<int,array{id:int,code:string,name:string,sections:array<int,array<string,mixed>>}>,
     *   total: int,
     *   stats: array{total:int,active:int,not_active:int,last_updated:?string},
     *   statusOptions: array<int,array<string,mixed>>,
     *   departments: array<int,array<string,mixed>>,
     *   q: string,
     *   status: int,
     *   open: int,
     *   filtering: bool,
     *   old: ?array<string,mixed>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleSection(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', SECTION_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                sectionBack('error', 'Tindakan tidak sah.');
            }
            sectionSave($action);
        }

        // ---------- Normal page view ----------
        $query  = sectionListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $rows = SectionModel::search($q, $status);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['section_old'] ?? null;
        unset($_SESSION['section_old']);

        return [
            'groups'        => sectionGroups($rows),
            'total'         => count($rows),
            'stats'         => SectionModel::stats(),
            'statusOptions' => SectionModel::statusOptions(),
            'departments'   => SectionModel::departmentOptions(),
            'q'             => $q,
            'status'        => $status,
            'open'          => (int) ($query['open'] ?? 0),
            'filtering'     => $q !== '' || $status > 0,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
