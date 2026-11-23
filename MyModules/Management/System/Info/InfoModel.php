<?php
declare(strict_types=1);

/**
 * InfoModel: database queries for the System Info page
 * (MyModules/Management/System/Info/Info.php).
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: system_info (one row only), ref_department.
 */
final class InfoModel
{
    /** Columns the user can edit (written by create() and update()). */
    private const FIELDS = [
        'system_name', 'system_short_name', 'system_version',
        'organization_name', 'system_department',
        'support_email', 'support_phone', 'logo_path', 'agency_logo_path', 'copyright_text',
    ];

    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = self::FIELDS;

    /** The system settings row. There is only one: null until the first save. */
    public static function get(): ?array
    {
        $row = db()->query('SELECT * FROM system_info ORDER BY system_info_id ASC LIMIT 1')->fetch();

        return $row ?: null;
    }

    /**
     * Active departments for the "Jabatan" dropdown.
     *
     * @return array<int,array{ref_department_id:int,ref_department_code:string,ref_department_description:string}>
     */
    public static function departmentOptions(): array
    {
        $stmt = db()->prepare(
            'SELECT ref_department_id, ref_department_code, ref_department_description
             FROM ref_department
             WHERE ref_status_id = :active
             ORDER BY ref_department_description ASC'
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchAll();
    }

    /** Is this an active department? */
    public static function departmentExists(int $id): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_department WHERE ref_department_id = :id AND ref_status_id = :active LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /** Database name and version, e.g. "MariaDB 10.4.32". */
    public static function databaseVersion(): string
    {
        $version = (string) db()->query('SELECT VERSION()')->fetchColumn();

        return stripos($version, 'mariadb') !== false
            ? 'MariaDB ' . (string) preg_replace('/-.*/', '', $version)
            : 'MySQL ' . $version;
    }

    /**
     * Insert the first settings row. Returns its ID.
     *
     * @param array<string,mixed> $data Keys from FIELDS
     */
    public static function create(array $data, ?int $userId): int
    {
        $columns = self::FIELDS;
        $sql = 'INSERT INTO system_info (' . implode(', ', $columns) . ', created_by, updated_by)
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
            'UPDATE system_info SET ' . implode(', ', $sets) . ', updated_by = :updated_by WHERE system_info_id = :id'
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
