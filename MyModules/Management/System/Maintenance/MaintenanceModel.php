<?php
declare(strict_types=1);

/**
 * MaintenanceModel: database queries for the Maintenance page
 * (MyModules/Management/System/Maintenance/Maintenance.php).
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: maintenance (one row only), ref_role, ref_status.
 */
final class MaintenanceModel
{
    /** One allow_* column for each role code in ref_role. The code is the key, so the column name is never built from user input. */
    public const ROLE_COLUMNS = [
        'SUPERADMIN' => 'allow_superadmin',
        'ADMIN'      => 'allow_admin',
        'USERS'      => 'allow_users',
        'GUEST'      => 'allow_guest',
        'REVIEWER'   => 'allow_reviewer',
        'APPROVER'   => 'allow_approver',
    ];

    /** Columns the user can edit (written by create() and update()). */
    private const FIELDS = [
        'maintenance_status_id', 'maintenance_start_date', 'maintenance_end_date',
        'maintenance_officer_id', 'maintenance_message',
        'allow_superadmin', 'allow_admin', 'allow_users',
        'allow_guest', 'allow_reviewer', 'allow_approver',
    ];

    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = self::FIELDS;

    /** The settings row. There is only one: null if the table is empty. */
    public static function get(): ?array
    {
        maintenanceExpire(); // end time passed: status becomes 2 automatically
        $row = db()->query('SELECT * FROM maintenance ORDER BY maintenance_id ASC LIMIT 1')->fetch();

        return $row ?: null;
    }

    /**
     * Active roles for the "Peranan Yang Boleh Akses Sistem" checkboxes.
     * Only roles that have an allow_* column are returned.
     *
     * @return array<int,array{ref_role_id:int,ref_role_code:string,ref_role_description:string}>
     */
    public static function roleOptions(): array
    {
        $stmt = db()->prepare(
            'SELECT ref_role_id, ref_role_code, ref_role_description
             FROM ref_role
             WHERE ref_status_id = :active
             ORDER BY ref_role_id ASC'
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return array_values(array_filter(
            $stmt->fetchAll(),
            static fn (array $r): bool => isset(self::ROLE_COLUMNS[$r['ref_role_code']])
        ));
    }

    /**
     * Insert the first settings row. Returns its ID.
     *
     * @param array<string,mixed> $data Keys from FIELDS
     */
    public static function create(array $data, ?int $userId): int
    {
        $columns = self::FIELDS;
        $sql = 'INSERT INTO maintenance (' . implode(', ', $columns) . ', created_by, updated_by)
                VALUES (' . implode(', ', array_map(static fn ($c) => ':' . $c, $columns)) . ', :created_by, :updated_by)';

        $params = ['created_by' => $userId, 'updated_by' => $userId];
        foreach ($columns as $c) {
            $params[$c] = $data[$c] ?? null;
        }
        db()->prepare($sql)->execute($params);

        return (int) db()->lastInsertId();
    }

    /**
     * Update the settings row.
     *
     * @param array<string,mixed> $data Keys from FIELDS
     */
    public static function update(int $id, array $data, ?int $userId): void
    {
        $sets   = [];
        $params = ['id' => $id, 'updated_by' => $userId];
        foreach (self::FIELDS as $c) {
            $sets[]     = "{$c} = :{$c}";
            $params[$c] = $data[$c] ?? null;
        }

        db()->prepare(
            'UPDATE maintenance SET ' . implode(', ', $sets) . ', updated_by = :updated_by WHERE maintenance_id = :id'
        )->execute($params);
    }

    /**
     * Only the fields worth keeping in the audit log.
     *
     * @param  array<string,mixed> $row
     * @return array<string,mixed>
     */
    public static function auditSnapshot(array $row): array
    {
        return array_intersect_key($row, array_flip(self::AUDIT_FIELDS));
    }
}
