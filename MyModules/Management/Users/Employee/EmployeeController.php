<?php
declare(strict_types=1);

/**
 * EmployeeController: the logic of the Employee page (Employee.php).
 *
 * Employee.php calls handleEmployee() once. The function:
 *   - on a normal page view (GET): returns the rows, statistics, pagination, the dropdown
 *     choices and the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS
 *     redirects (Post/Redirect/Get), so pressing Refresh never re-sends the form.
 *
 * An employee is a row of the `users` table. The date of birth and the age are NEVER
 * taken from the form: they are worked out from the IC number (myID) on the server.
 * The password is required when adding, and optional when editing (blank = keep the old one).
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits). Every action is recorded:
 *   CREATED        an employee was added
 *   UPDATED        an employee was changed (only the changed fields are saved)
 *   NO_CHANGE      the edit form was saved without any change
 *   CREATE_FAILED  adding was refused (validation / duplicate / database error)
 *   UPDATE_FAILED  editing was refused (validation / not found / duplicate / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no record exists yet).
 * The password is never written to the audit log.
 */

require_once __DIR__ . '/EmployeeModel.php';

const EMPLOYEE_TABLE    = 'users';
const EMPLOYEE_NAME_MAX = 255;
const EMPLOYEE_MAIL_MAX = 150;
const EMPLOYEE_EXT_MAX  = 20;
const EMPLOYEE_PER_PAGE = 10;

if (!function_exists('employeeListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function employeeListQuery(array $source): array
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

if (!function_exists('employeeBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function employeeBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['employee_old'] = $old;
        }
        redirect(routeUrl('users-employee', employeeListQuery($_POST)), 303);
    }
}

if (!function_exists('employeeFail')) {
    /**
     * A save was refused: write it to the audit log (without the password), then go back.
     *
     * @param array<string,mixed> $old Typed values (the password is not part of it)
     */
    function employeeFail(string $event, int $id, string $message, array $old): never
    {
        audit($event, EMPLOYEE_TABLE, $id, null, [
            'reason'   => $message,
            'myID'     => $old['myID'],
            'fullname' => $old['fullname'],
            'email'    => $old['email'],
        ]);
        employeeBack('error', $message, $old);
    }
}

if (!function_exists('employeeSave')) {
    /** Handle POST action=create and action=update. */
    function employeeSave(string $action): never
    {
        $isEdit = $action === 'update';
        $failed = $isEdit ? 'UPDATE_FAILED' : 'CREATE_FAILED';

        $id = $isEdit ? toInt($_POST['id'] ?? 0) : 0;

        // Clean what the user typed (the IC keeps digits only; the name loses double spaces)
        $myID     = cleanMyID((string) ($_POST['myID'] ?? ''));
        $fullname = mb_strtoupper(trim((string) preg_replace('/\s+/u', ' ', (string) ($_POST['fullname'] ?? ''))));
        $email    = mb_strtolower(trim((string) ($_POST['email'] ?? '')));
        $phone    = trim((string) ($_POST['phone_number'] ?? ''));
        $ext      = trim((string) ($_POST['office_extension'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $d = [
            'myID'       => $myID,
            'fullname'   => $fullname,
            'dob'        => null,
            'age'        => null,
            'email'      => $email,
            'phone'      => $phone,
            'ext'        => $ext === '' ? null : $ext,
            'department' => toInt($_POST['ref_department_id'] ?? 0),
            'section'    => toInt($_POST['ref_section_id'] ?? 0),
            'division'   => toInt($_POST['ref_division_id'] ?? 0),
            'branch'     => toInt($_POST['ref_branch_id'] ?? 0),
            'role'       => toInt($_POST['ref_role_id'] ?? 0),
            'role_type'  => toInt($_POST['ref_role_type_id'] ?? 0),
            'status'     => toInt($_POST['status_id'] ?? 0),
        ];

        // What is sent back to re-open the form (never the password)
        $old = [
            'action'           => $action,
            'id'               => $id,
            'myID'             => $myID,
            'fullname'         => $fullname,
            'email'            => $email,
            'phone_number'     => $phone,
            'office_extension' => $ext,
            'ref_department_id' => $d['department'],
            'ref_section_id'    => $d['section'],
            'ref_division_id'   => $d['division'],
            'ref_branch_id'     => $d['branch'],
            'ref_role_id'       => $d['role'],
            'ref_role_type_id'  => $d['role_type'],
            'status'            => $d['status'],
        ];

        // 1. Input check
        $current = null;
        if ($isEdit) {
            $current = $id > 0 ? EmployeeModel::findById($id) : null;
            if ($current === null) {
                employeeFail($failed, $id, 'Rekod kakitangan tidak ditemui.', $old);
            }
        }

        if ($fullname === '') {
            employeeFail($failed, $id, 'Sila isi Nama Penuh.', $old);
        }
        if (mb_strlen($fullname) > EMPLOYEE_NAME_MAX) {
            employeeFail($failed, $id, 'Nama Penuh terlalu panjang (maksimum ' . EMPLOYEE_NAME_MAX . ' aksara).', $old);
        }
        if (!isValidMyID($myID)) {
            employeeFail($failed, $id, 'No. Kad Pengenalan tidak sah (12 digit dan tarikh lahir yang betul).', $old);
        }
        if ($email === '' || mb_strlen($email) > EMPLOYEE_MAIL_MAX || !isValidEmail($email)) {
            employeeFail($failed, $id, 'Sila masukkan Email yang sah.', $old);
        }
        if (!isValidPhone($phone)) {
            employeeFail($failed, $id, 'No. Telefon tidak sah (cth: 012-345 6789).', $old);
        }
        if ($ext !== '' && (!preg_match('/^\d+$/', $ext) || strlen($ext) > EMPLOYEE_EXT_MAX)) {
            employeeFail($failed, $id, 'Sambungan (ext.) mesti nombor sahaja (maksimum ' . EMPLOYEE_EXT_MAX . ' digit).', $old);
        }

        $labels = [
            'department' => ['Jabatan', 'department'], 'section' => ['Seksyen', 'section'],
            'division'   => ['Bahagian', 'division'], 'branch'  => ['Cawangan', 'branch'],
            'role'       => ['Peranan', 'role'],      'role_type' => ['Jenis Peranan', 'role_type'],
        ];
        foreach ($labels as $key => [$label, $refName]) {
            // An edit may keep a value that has since become inactive
            $unchanged = $current !== null && (int) ($current['ref_' . $refName . '_id'] ?? 0) === $d[$key];
            if ($d[$key] <= 0 || (!$unchanged && !EmployeeModel::refExists($refName, $d[$key]))) {
                employeeFail($failed, $id, "Sila pilih {$label} yang sah.", $old);
            }
        }
        if (!EmployeeModel::sectionInDepartment($d['section'], $d['department'])) {
            employeeFail($failed, $id, 'Seksyen yang dipilih bukan di bawah Jabatan tersebut.', $old);
        }
        if (!EmployeeModel::isValidStatus($d['status'])) {
            employeeFail($failed, $id, 'Status tidak sah.', $old);
        }

        // Password: required when adding; when editing, blank means "keep the old one"
        if (!$isEdit && $password === '') {
            employeeFail($failed, $id, 'Sila isi Kata Laluan.', $old);
        }
        if ($password !== '') {
            $problems = validatePasswordStrength($password);
            if ($problems !== []) {
                employeeFail($failed, $id, 'Kata Laluan tidak cukup kukuh: ' . implode(' ', $problems), $old);
            }
        }

        if (EmployeeModel::myIdExists($myID, $id)) {
            employeeFail($failed, $id, 'No. Kad Pengenalan ' . formatMyID($myID) . ' sudah wujud.', $old);
        }
        if (EmployeeModel::emailExists($email, $id)) {
            employeeFail($failed, $id, "Email {$email} sudah digunakan.", $old);
        }

        // DOB and age always come from the IC number, never from the form
        $d['dob'] = extractDobFromMyID($myID);
        $d['age'] = getAgeFromMyID($myID);

        // 2. Save + audit
        $userId = currentUserId();
        $new    = [
            'myID'              => $myID,
            'fullname'          => $fullname,
            'dob'               => $d['dob'],
            'age'               => $d['age'],
            'email'             => $email,
            'phone_number'      => $phone,
            'office_extension'  => $d['ext'],
            'ref_department_id' => $d['department'],
            'ref_section_id'    => $d['section'],
            'ref_division_id'   => $d['division'],
            'ref_branch_id'     => $d['branch'],
            'ref_role_id'       => $d['role'],
            'ref_role_type_id'  => $d['role_type'],
            'ref_status_id'     => $d['status'],
        ];

        try {
            if ($isEdit) {
                $before = EmployeeModel::auditSnapshot($current);

                // Nothing changed (and no new password): keep a trace, but do not touch the database
                [, $changed] = auditDiff($before, $new);
                if ($changed === [] && $password === '') {
                    audit('NO_CHANGE', EMPLOYEE_TABLE, $id, null, $new);
                    employeeBack('info', 'Tiada perubahan dibuat.');
                }

                EmployeeModel::update($id, $d, $password !== '' ? hashPassword($password) : null, $userId);
                // The password itself is never logged; only the fact that it was changed
                audit('UPDATED', EMPLOYEE_TABLE, $id, $before, $new + ($password !== '' ? ['password_changed' => true] : []));
                employeeBack('success', "Kakitangan {$fullname} berjaya dikemas kini.");
            }

            $newId = EmployeeModel::create($d, hashPassword($password), $userId);
            audit('CREATED', EMPLOYEE_TABLE, $newId, null, $new);
            employeeBack('success', "Kakitangan {$fullname} berjaya ditambah.");
        } catch (PDOException $e) {
            error_log('[EMPLOYEE] ' . $e->getMessage());
            employeeFail($failed, $id, 'Ralat pangkalan data. Sila cuba lagi.', $old);
        }
    }
}

if (!function_exists('handleEmployee')) {
    /**
     * Run the Employee page logic.
     *
     * @return array{
     *   rows: array<int,array<string,mixed>>,
     *   stats: array{total:int,active:int,not_active:int,last_updated:?string},
     *   page: array<string,mixed>,
     *   statusOptions: array<int,array<string,mixed>>,
     *   departments: array<int,array<string,mixed>>,
     *   sections: array<int,array<string,mixed>>,
     *   divisions: array<int,array<string,mixed>>,
     *   branches: array<int,array<string,mixed>>,
     *   roles: array<int,array<string,mixed>>,
     *   roleTypes: array<int,array<string,mixed>>,
     *   q: string,
     *   status: int,
     *   old: ?array<string,mixed>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleEmployee(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if (!in_array($action, ['create', 'update'], true)) {
                audit('INVALID_ACTION', EMPLOYEE_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                employeeBack('error', 'Tindakan tidak sah.');
            }
            employeeSave($action);
        }

        // ---------- Normal page view ----------
        $query  = employeeListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(EmployeeModel::count($q, $status), currentPage(), EMPLOYEE_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['employee_old'] ?? null;
        unset($_SESSION['employee_old']);

        return [
            'rows'          => EmployeeModel::search($q, $status, $page['per_page'], $page['offset']),
            'stats'         => EmployeeModel::stats(),
            'page'          => $page,
            'statusOptions' => EmployeeModel::statusOptions(),
            'departments'   => EmployeeModel::departmentOptions(),
            'sections'      => EmployeeModel::sectionOptions(),
            'divisions'     => EmployeeModel::divisionOptions(),
            'branches'      => EmployeeModel::branchOptions(),
            'roles'         => EmployeeModel::roleOptions(),
            'roleTypes'     => EmployeeModel::roleTypeOptions(),
            'q'             => $q,
            'status'        => $status,
            'old'           => is_array($old) ? $old : null,
            'flash'         => pullFlash(),
        ];
    }
}
