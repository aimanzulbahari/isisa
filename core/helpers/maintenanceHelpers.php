<?php
declare(strict_types=1);

/**
 * Maintenance helpers: is the system under maintenance right now, and may this role still enter?
 *
 * The settings come from the maintenance table (one row), saved on the page
 * Pengurusan > Sistem > Penyelenggaraan (MyModules/Management/System/Maintenance).
 * Maintenance is ON only when the status is ACTIVE and the time now is inside the
 * start / end time. Each role has an allow_* column: 1 = may still log in and work.
 * SUPERADMIN is always allowed, so nobody can lock the system for good.
 *
 * Used by: requireAuth() (a logged-in user whose role is blocked is logged out) and the
 * login page (a blocked role cannot log in). Visitors who are not logged in still see the login page.
 *
 * If the maintenance table cannot be read (missing table, database error), the system is
 * NOT blocked: a broken setting must never take the whole system down.
 */

if (!function_exists('maintenanceExpire')) {
    /**
     * Switch maintenance OFF (status 2 = NOT_ACTIVE) once its end date and time has passed.
     * Safe to call many times: it only changes rows that are still ACTIVE and already ended.
     * Called by maintenanceCurrent() and MaintenanceModel::get(), so the status is always up to date.
     */
    function maintenanceExpire(): void
    {
        static $done = false;
        if ($done) {
            return;
        }
        $done = true;

        try {
            db()->prepare(
                'UPDATE maintenance
                 SET maintenance_status_id = :not_active
                 WHERE maintenance_status_id = :active
                   AND maintenance_end_date IS NOT NULL
                   AND maintenance_end_date < :now'
            )->execute([
                'not_active' => config('app.status')['NOT_ACTIVE'],
                'active'     => config('app.status')['ACTIVE'],
                'now'        => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            error_log('[MAINTENANCE] Could not switch off expired maintenance: ' . $e->getMessage());
        }
    }
}

if (!function_exists('maintenanceCurrent')) {
    /**
     * The maintenance row if maintenance is ON right now, otherwise null.
     * Read once per request.
     *
     * @return array<string,mixed>|null
     */
    function maintenanceCurrent(): ?array
    {
        static $loaded = false;
        static $row    = null;

        if ($loaded) {
            return $row;
        }
        $loaded = true;

        maintenanceExpire(); // end time passed: status becomes 2 automatically

        try {
            $now  = date('Y-m-d H:i:s');
            $stmt = db()->prepare(
                'SELECT m.*, u.fullname AS officer_name
                 FROM maintenance m
                 LEFT JOIN users u ON u.users_id = m.maintenance_officer_id
                 WHERE m.maintenance_status_id = :active
                   AND (m.maintenance_start_date IS NULL OR m.maintenance_start_date <= :now_start)
                   AND (m.maintenance_end_date IS NULL OR m.maintenance_end_date >= :now_end)
                 ORDER BY m.maintenance_id ASC
                 LIMIT 1'
            );
            $stmt->execute([
                'active'    => config('app.status')['ACTIVE'],
                'now_start' => $now,
                'now_end'   => $now,
            ]);
            $row = $stmt->fetch() ?: null;
        } catch (Throwable $e) {
            error_log('[MAINTENANCE] Could not read the maintenance setting: ' . $e->getMessage());
            $row = null;
        }

        return $row;
    }
}

if (!function_exists('maintenanceBlocksRole')) {
    /**
     * Does maintenance block this role right now?
     *
     * @param  string|null $roleCode Role code from ref_role, e.g. "ADMIN"
     * @return array<string,mixed>|null The maintenance row when the role is blocked, null when it may enter.
     */
    function maintenanceBlocksRole(?string $roleCode): ?array
    {
        $row = maintenanceCurrent();
        if ($row === null) {
            return null;
        }

        $code = strtolower((string) $roleCode);
        if ($code === 'superadmin') {
            return null;
        }
        // Only these roles have an allow_* column. Any other role is blocked.
        if (!in_array($code, ['admin', 'users', 'guest', 'reviewer', 'approver'], true)) {
            return $row;
        }

        return (int) ($row['allow_' . $code] ?? 0) === 1 ? null : $row;
    }
}

if (!function_exists('maintenanceMessage')) {
    /**
     * The message the admin saved, or a default text when it is empty.
     *
     * @param array<string,mixed> $row A maintenance row
     */
    function maintenanceMessage(array $row): string
    {
        $message = trim((string) ($row['maintenance_message'] ?? ''));

        return $message !== '' ? $message : 'Sistem ISISA sedang dalam penyelenggaraan. Sila cuba semula selepas penyelenggaraan tamat.';
    }
}

if (!function_exists('maintenanceDetails')) {
    /**
     * What the maintenance popup on the login page shows (plain text, escape it when printing).
     * An empty string means "nothing to show" and the popup leaves that row out.
     *
     * @param  array<string,mixed> $row A maintenance row from maintenanceCurrent()
     * @return array{message:string,start:string,end:string,officer:string}
     */
    function maintenanceDetails(array $row): array
    {
        $format  = static fn (?string $value): string => !empty($value) && ($t = strtotime($value)) !== false ? date('d/m/Y, h:i A', $t) : '';
        $officer = trim((string) ($row['officer_name'] ?? ''));
        // A name typed all in CAPITALS (or all lowercase) is tidied up, like userDisplayName()
        if ($officer !== '' && ($officer === mb_strtoupper($officer) || $officer === mb_strtolower($officer))) {
            $officer = properName($officer);
        }

        return [
            'message' => maintenanceMessage($row),
            'start'   => $format($row['maintenance_start_date'] ?? null),
            'end'     => $format($row['maintenance_end_date'] ?? null),
            'officer' => $officer,
        ];
    }
}

if (!function_exists('maintenanceNotice')) {
    /**
     * The text shown to a blocked user on the 503 page: the saved message (or a default) and the expected end time.
     *
     * @param array<string,mixed> $row A maintenance row
     */
    function maintenanceNotice(array $row): string
    {
        $message = maintenanceMessage($row);

        if (!empty($row['maintenance_end_date'])) {
            $end = strtotime((string) $row['maintenance_end_date']);
            if ($end !== false) {
                $message .= ' Dijangka siap pada ' . date('d/m/Y h:i A', $end) . '.';
            }
        }
        return $message;
    }
}
