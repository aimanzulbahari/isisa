<?php
declare(strict_types=1);

/**
 * MaintenanceController: the logic of the Maintenance page (Maintenance.php).
 *
 * Maintenance.php calls handleMaintenance() once. The function:
 *   - only lets ADMIN / SUPERADMIN in (anyone else gets 403 and the attempt is audited)
 *   - on a normal page view (GET): returns the saved settings, the role checkboxes and the
 *     messages to show
 *   - on a form submit (POST): validates, saves, writes the audit log, then ALWAYS redirects
 *     (Post/Redirect/Get)
 *
 * The maintenance table holds ONE row. The first save inserts it, the next saves update it.
 * The responsible officer is always the logged-in user who saves (it is not typed in the form).
 * SUPERADMIN can never be locked out: its checkbox is always saved as allowed.
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits, auditable_type = maintenance):
 *   CREATED        the settings were saved for the first time
 *   UPDATED        the settings were changed (only the changed fields are saved)
 *   NO_CHANGE      the form was saved without any change
 *   UPDATE_FAILED  saving was refused (validation / database error)
 *   ACCESS_DENIED  a user without the right role tried to open the page
 * audits.auditable_id is required, so a failed first save uses 0 (no row exists yet).
 */

require_once __DIR__ . '/MaintenanceModel.php';

const MAINTENANCE_TABLE       = 'maintenance';
const MAINTENANCE_MESSAGE_MAX = 500;   // same as maxlength in the form

if (!function_exists('maintenanceBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the page.
     *
     * @param array<string,mixed>|null $old Typed values (form field names), to fill the form again
     */
    function maintenanceBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['maintenance_old'] = $old;
        }
        redirect(routeUrl('system-maintenance'), 303);
    }
}

if (!function_exists('maintenanceFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (form field names)
     */
    function maintenanceFail(int $id, string $message, array $old): never
    {
        audit('UPDATE_FAILED', MAINTENANCE_TABLE, $id, null, ['reason' => $message] + $old);
        maintenanceBack('error', $message, $old);
    }
}

if (!function_exists('maintenanceText')) {
    /** A text input: trimmed, spaces collapsed (line breaks kept for multi-line fields). */
    function maintenanceText(string $key, bool $multiline = false): string
    {
        $value = $_POST[$key] ?? '';
        $value = is_string($value) ? trim($value) : '';

        return $multiline
            ? trim((string) preg_replace('/[ \t]+/u', ' ', str_replace("\r\n", "\n", $value)))
            : trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}

if (!function_exists('maintenanceDateTime')) {
    /**
     * A date & time from the form ("2026-11-20T09:30" from datetime-local) -> "2026-11-20 09:30:00".
     * Returns null if it is not a real date and time.
     */
    function maintenanceDateTime(string $value): ?string
    {
        foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i', 'Y-m-d H:i:s'] as $format) {
            $date   = DateTimeImmutable::createFromFormat('!' . $format, $value);
            $errors = DateTimeImmutable::getLastErrors();
            // The warning check rejects values such as "2026-02-31T10:00"
            if ($date && (!$errors || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d H:i:s');
            }
        }
        return null;
    }
}

if (!function_exists('maintenanceSave')) {
    /** Handle the form POST. */
    function maintenanceSave(): never
    {
        $current = MaintenanceModel::get();
        $id      = $current !== null ? (int) $current['maintenance_id'] : 0;
        $roles   = MaintenanceModel::roleOptions();

        // 1. Read the typed values (keys = form field names, so the form can be filled again)
        $postedRoles = $_POST['peranan'] ?? [];
        $typed = [
            'status_penyelenggaraan' => maintenanceText('status_penyelenggaraan'),
            'tarikh_mula'            => maintenanceText('tarikh_mula'),
            'tarikh_tamat'           => maintenanceText('tarikh_tamat'),
            'mesej'                  => maintenanceText('mesej', true),
            'peranan'                => is_array($postedRoles)
                ? array_values(array_filter($postedRoles, 'is_string'))
                : [],
        ];

        // 2. Input check
        $active = match ($typed['status_penyelenggaraan']) {
            'aktif'       => true,
            'tidak-aktif' => false,
            default       => maintenanceFail($id, 'Sila pilih Status Penyelenggaraan yang sah.', $typed),
        };

        if (($_POST['pengesahan'] ?? '') !== '1') {
            maintenanceFail($id, 'Sila tandakan akuan pengesahan.', $typed);
        }

        // Dates are required when the maintenance is switched on, and checked whenever they are given
        $start = null;
        $end   = null;
        if ($typed['tarikh_mula'] !== '') {
            $start = maintenanceDateTime($typed['tarikh_mula']);
            if ($start === null) {
                maintenanceFail($id, 'Tarikh & Masa Mula tidak sah.', $typed);
            }
        }
        if ($typed['tarikh_tamat'] !== '') {
            $end = maintenanceDateTime($typed['tarikh_tamat']);
            if ($end === null) {
                maintenanceFail($id, 'Tarikh & Masa Tamat tidak sah.', $typed);
            }
        }
        if ($active) {
            if ($start === null) {
                maintenanceFail($id, 'Sila pilih Tarikh & Masa Mula.', $typed);
            }
            if ($end === null) {
                maintenanceFail($id, 'Sila pilih Tarikh & Masa Tamat.', $typed);
            }
        }
        if ($start !== null && $end !== null && $end <= $start) {
            maintenanceFail($id, 'Tarikh & Masa Tamat mesti selepas Tarikh & Masa Mula.', $typed);
        }
        if ($active && $end !== null && $end <= date('Y-m-d H:i:s')) {
            maintenanceFail($id, 'Tarikh & Masa Tamat sudah berlalu.', $typed);
        }

        if (mb_strlen($typed['mesej']) > MAINTENANCE_MESSAGE_MAX) {
            maintenanceFail($id, 'Mesej Penyelenggaraan terlalu panjang (maksimum ' . MAINTENANCE_MESSAGE_MAX . ' aksara).', $typed);
        }

        // Roles: every ticked value must be a real role from ref_role (the value is the role code, lowercase)
        $validCodes = array_map(static fn (array $r): string => strtolower($r['ref_role_code']), $roles);
        foreach ($typed['peranan'] as $code) {
            if (!in_array($code, $validCodes, true)) {
                maintenanceFail($id, 'Peranan yang dipilih tidak sah.', $typed);
            }
        }

        // 3. Save. A role that is not ticked is saved as 0. SUPERADMIN is always allowed.
        $new = [
            'maintenance_status_id'  => config('app.status')[$active ? 'ACTIVE' : 'NOT_ACTIVE'],
            'maintenance_start_date' => $start,
            'maintenance_end_date'   => $end,
            'maintenance_officer_id' => currentUserId(),
            'maintenance_message'    => $typed['mesej'] !== '' ? $typed['mesej'] : null,
        ];
        foreach (MaintenanceModel::ROLE_COLUMNS as $roleCode => $column) {
            $new[$column] = $roleCode === 'SUPERADMIN' || in_array(strtolower($roleCode), $typed['peranan'], true) ? 1 : 0;
        }

        $userId = currentUserId();
        try {
            if ($current === null) {
                $newId = MaintenanceModel::create($new, $userId);
                audit('CREATED', MAINTENANCE_TABLE, $newId, null, $new);
                maintenanceBack('success', 'Tetapan penyelenggaraan berjaya disimpan.');
            }

            $before = MaintenanceModel::auditSnapshot($current);
            [, $changed] = auditDiff($before, $new);
            if ($changed === []) {
                audit('NO_CHANGE', MAINTENANCE_TABLE, $id, null, $new);
                maintenanceBack('info', 'Tiada perubahan dibuat.');
            }

            MaintenanceModel::update($id, $new, $userId);
            audit('UPDATED', MAINTENANCE_TABLE, $id, $before, $new);
            maintenanceBack('success', 'Tetapan penyelenggaraan berjaya dikemas kini.');
        } catch (PDOException $e) {
            error_log('[MAINTENANCE] ' . $e->getMessage());
            maintenanceFail($id, 'Ralat pangkalan data. Sila cuba lagi.', $typed);
        }
    }
}

if (!function_exists('handleMaintenance')) {
    /**
     * Run the Maintenance page logic.
     *
     * @return array{
     *   form: array{status:string,start:string,end:string,message:string,roles:array<int,string>},
     *   officer: string,
     *   roles: array<int,array<string,mixed>>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     * "form" is what the form shows: the last refused input if there is one, else the saved settings.
     */
    function handleMaintenance(): array
    {
        // Only admins may see or change the maintenance settings
        requireAuth();
        if (!isAdmin()) {
            audit('ACCESS_DENIED', MAINTENANCE_TABLE, 0, null, ['role' => currentRole(), 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']);
            respondError(403, 'You do not have permission to access this page.');
        }

        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            maintenanceSave();
        }

        // ---------- Normal page view ----------
        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['maintenance_old'] ?? null;
        unset($_SESSION['maintenance_old']);

        if (is_array($old)) {
            $form = [
                'status'  => (string) ($old['status_penyelenggaraan'] ?? 'tidak-aktif'),
                'start'   => (string) ($old['tarikh_mula'] ?? ''),
                'end'     => (string) ($old['tarikh_tamat'] ?? ''),
                'message' => (string) ($old['mesej'] ?? ''),
                'roles'   => array_map('strval', (array) ($old['peranan'] ?? [])),
            ];
        } else {
            $saved = MaintenanceModel::get();
            $roles = [];
            foreach (MaintenanceModel::ROLE_COLUMNS as $roleCode => $column) {
                // No saved row yet: only SUPERADMIN is allowed (the same as the default in the table)
                if ($saved !== null ? (int) $saved[$column] === 1 : $roleCode === 'SUPERADMIN') {
                    $roles[] = strtolower($roleCode);
                }
            }
            $form = [
                'status'  => $saved !== null && (int) $saved['maintenance_status_id'] === config('app.status')['ACTIVE'] ? 'aktif' : 'tidak-aktif',
                // datetime-local needs "Y-m-d\TH:i"
                'start'   => !empty($saved['maintenance_start_date']) ? date('Y-m-d\TH:i', strtotime((string) $saved['maintenance_start_date'])) : '',
                'end'     => !empty($saved['maintenance_end_date']) ? date('Y-m-d\TH:i', strtotime((string) $saved['maintenance_end_date'])) : '',
                'message' => (string) ($saved['maintenance_message'] ?? ''),
                'roles'   => $roles,
            ];
        }

        return [
            'form'    => $form,
            'officer' => userDisplayName(),
            'roles'   => MaintenanceModel::roleOptions(),
            'flash'   => pullFlash(),
        ];
    }
}
