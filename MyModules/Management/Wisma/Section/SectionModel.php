<?php
declare(strict_types=1);

/**
 * SectionModel: database queries for the Section page
 * (MyModules/Management/Wisma/Section/Section.php).
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: ref_section, ref_department, ref_status, users.
 *
 * A section always belongs to ONE department (ref_section.ref_department_id).
 * Columns: ref_section_code (unique), ref_section_name (required),
 * ref_section_description (optional, max 300).
 */
final class SectionModel
{
    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = [
        'ref_department_id', 'ref_section_code', 'ref_section_name', 'ref_section_description', 'ref_status_id',
    ];

    /**
     * WHERE part for search().
     *
     * @return array{0:string,1:array<string,mixed>} [sql, params]
     */
    private static function where(string $q, int $statusId): array
    {
        $sql    = ' WHERE 1 = 1';
        $params = [];

        if ($q !== '') {
            // Escape % and _ so they are searched as normal characters
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $sql .= ' AND (s.ref_section_code LIKE :q1 OR s.ref_section_name LIKE :q2
                           OR s.ref_section_description LIKE :q3 OR d.ref_department_description LIKE :q4)';
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
            $params['q4'] = $like;
        }
        if ($statusId > 0) {
            $sql .= ' AND s.ref_status_id = :status';
            $params['status'] = $statusId;
        }
        return [$sql, $params];
    }

    /**
     * Every section that matches the search / filter, with its department.
     * Ordered by department name, then section name, so the controller can group them.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function search(string $q, int $statusId): array
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare(
            'SELECT s.ref_section_id, s.ref_department_id, s.ref_section_code, s.ref_section_name,
                    s.ref_section_description, s.ref_status_id, s.created_at, s.updated_at,
                    d.ref_department_code, d.ref_department_description,
                    st.ref_status_description
             FROM ref_section s
             JOIN ref_department d       ON d.ref_department_id = s.ref_department_id
             LEFT JOIN ref_status st     ON st.ref_status_id = s.ref_status_id'
            . $where .
            ' ORDER BY d.ref_department_description ASC, d.ref_department_id ASC, s.ref_section_name ASC, s.ref_section_id ASC'
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Numbers for the 4 statistic cards.
     *
     * @return array{total:int,active:int,not_active:int,last_updated:?string}
     */
    public static function stats(): array
    {
        $status = config('app.status');

        $stmt = db()->prepare(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(ref_status_id = :active), 0)     AS active,
                    COALESCE(SUM(ref_status_id = :not_active), 0) AS not_active,
                    MAX(updated_at)                               AS last_updated
             FROM ref_section'
        );
        $stmt->execute(['active' => $status['ACTIVE'], 'not_active' => $status['NOT_ACTIVE']]);
        $row = $stmt->fetch() ?: [];

        return [
            'total'        => (int) ($row['total'] ?? 0),
            'active'       => (int) ($row['active'] ?? 0),
            'not_active'   => (int) ($row['not_active'] ?? 0),
            'last_updated' => $row['last_updated'] ?? null,
        ];
    }

    /**
     * Status choices for the filter and the form: only ACTIVE and NOT_ACTIVE.
     *
     * @return array<int,array{ref_status_id:int,ref_status_description:string}>
     */
    public static function statusOptions(): array
    {
        $status = config('app.status');

        $stmt = db()->prepare(
            'SELECT ref_status_id, ref_status_description
             FROM ref_status
             WHERE ref_status_id IN (:active, :not_active)
             ORDER BY ref_status_id'
        );
        $stmt->execute(['active' => $status['ACTIVE'], 'not_active' => $status['NOT_ACTIVE']]);

        return $stmt->fetchAll();
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
    public static function departmentIsActive(int $id): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_department WHERE ref_department_id = :id AND ref_status_id = :active LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /** Is this ref_status ID one of the allowed choices (ACTIVE / NOT_ACTIVE)? */
    public static function isValidStatus(int $statusId): bool
    {
        $status = config('app.status');
        return in_array($statusId, [$status['ACTIVE'], $status['NOT_ACTIVE']], true);
    }

    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM ref_section WHERE ref_section_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Is this code already used? The code is unique in the whole table (excludeId lets an edit keep its own). */
    public static function codeExists(string $code, int $excludeId = 0): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_section
             WHERE ref_section_code = :code AND ref_section_id <> :id
             LIMIT 1'
        );
        $stmt->execute(['code' => $code, 'id' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /** Does this department already have a section with this name? (excludeId lets an edit keep its own) */
    public static function nameExistsInDepartment(string $name, int $departmentId, int $excludeId = 0): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_section
             WHERE ref_department_id = :dept AND ref_section_name = :name AND ref_section_id <> :id
             LIMIT 1'
        );
        $stmt->execute(['dept' => $departmentId, 'name' => $name, 'id' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /** How many employees (users) are placed in this section? */
    public static function employeeCount(int $sectionId): int
    {
        $stmt = db()->prepare('SELECT COUNT(*) FROM users WHERE ref_section_id = :id');
        $stmt->execute(['id' => $sectionId]);

        return (int) $stmt->fetchColumn();
    }

    /** Insert a section. Returns the new ID. */
    public static function create(
        int $departmentId,
        string $code,
        string $name,
        ?string $description,
        int $statusId,
        ?int $userId
    ): int {
        db()->prepare(
            'INSERT INTO ref_section
                (ref_department_id, ref_section_code, ref_section_name, ref_section_description,
                 ref_status_id, created_by, updated_by)
             VALUES
                (:dept, :code, :name, :description, :status, :user, :user2)'
        )->execute([
            'dept'        => $departmentId,
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'status'      => $statusId,
            'user'        => $userId,
            'user2'       => $userId,
        ]);

        return (int) db()->lastInsertId();
    }

    /** Update a section. */
    public static function update(
        int $id,
        int $departmentId,
        string $code,
        string $name,
        ?string $description,
        int $statusId,
        ?int $userId
    ): void {
        db()->prepare(
            'UPDATE ref_section
             SET ref_department_id = :dept,
                 ref_section_code = :code,
                 ref_section_name = :name,
                 ref_section_description = :description,
                 ref_status_id = :status,
                 updated_by = :user
             WHERE ref_section_id = :id'
        )->execute([
            'dept'        => $departmentId,
            'code'        => $code,
            'name'        => $name,
            'description' => $description,
            'status'      => $statusId,
            'user'        => $userId,
            'id'          => $id,
        ]);
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
