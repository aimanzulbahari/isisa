<?php
declare(strict_types=1);

/**
 * EmployeeModel: database queries for the Employee page
 * (MyModules/Management/Users/Employee/Employee.php).
 *
 * An employee is a row in the `users` table. Only talks to the database:
 * no sessions, redirects or HTML here.
 * Related tables: ref_department, ref_section, ref_division, ref_branch,
 * ref_role, ref_role_type, ref_status.
 */
final class EmployeeModel
{
    /** Columns saved in the audit log (a snapshot of a row). The password is never logged. */
    private const AUDIT_FIELDS = [
        'myID', 'fullname', 'dob', 'age', 'email', 'phone_number', 'office_extension',
        'ref_department_id', 'ref_section_id', 'ref_division_id', 'ref_branch_id',
        'ref_role_id', 'ref_role_type_id', 'ref_status_id',
    ];

    /**
     * WHERE part shared by search() and count().
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
            $sql .= ' AND (u.fullname LIKE :q1 OR u.myID LIKE :q2 OR u.email LIKE :q3)';
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }
        if ($statusId > 0) {
            $sql .= ' AND u.ref_status_id = :status';
            $params['status'] = $statusId;
        }
        return [$sql, $params];
    }

    /** Number of rows that match the search / filter (for pagination). */
    public static function count(string $q = '', int $statusId = 0): int
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare('SELECT COUNT(*) FROM users u' . $where);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of employees (the password hash is never selected).
     *
     * @return array<int,array<string,mixed>>
     */
    public static function search(string $q, int $statusId, int $limit, int $offset): array
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare(
            'SELECT u.users_id, u.myID, u.fullname, u.dob, u.age, u.email, u.phone_number, u.office_extension,
                    u.ref_department_id, u.ref_section_id, u.ref_division_id, u.ref_branch_id,
                    u.ref_role_id, u.ref_role_type_id, u.ref_status_id, u.updated_at,
                    d.ref_department_description, sec.ref_section_name,
                    r.ref_role_description, s.ref_status_description
             FROM users u
             LEFT JOIN ref_department d   ON d.ref_department_id = u.ref_department_id
             LEFT JOIN ref_section sec    ON sec.ref_section_id  = u.ref_section_id
             LEFT JOIN ref_role r         ON r.ref_role_id       = u.ref_role_id
             LEFT JOIN ref_status s       ON s.ref_status_id     = u.ref_status_id'
            . $where .
            ' ORDER BY u.users_id ASC
             LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

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
             FROM users'
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
     * Active choices of a reference table for a dropdown.
     * $table / $prefix come from the fixed list below, never from user input.
     *
     * @return array<int,array<string,mixed>>
     */
    private static function refOptions(string $table, string $prefix, string $extra = '', string $label = 'description'): array
    {
        // $label is the column that holds the text shown in the list (ref_section uses "name", the others "description")
        $stmt = db()->prepare(
            "SELECT {$prefix}_id, {$prefix}_{$label}{$extra}
             FROM {$table}
             WHERE ref_status_id = :active
             ORDER BY {$prefix}_{$label}"
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchAll();
    }

    /** @return array<int,array<string,mixed>> */
    public static function departmentOptions(): array
    {
        return self::refOptions('ref_department', 'ref_department');
    }

    /** Sections carry their department ID so the form can filter them. @return array<int,array<string,mixed>> */
    public static function sectionOptions(): array
    {
        return self::refOptions('ref_section', 'ref_section', ', ref_department_id', 'name');
    }

    /** @return array<int,array<string,mixed>> */
    public static function divisionOptions(): array
    {
        return self::refOptions('ref_division', 'ref_division');
    }

    /** @return array<int,array<string,mixed>> */
    public static function branchOptions(): array
    {
        return self::refOptions('ref_branch', 'ref_branch');
    }

    /** @return array<int,array<string,mixed>> */
    public static function roleOptions(): array
    {
        return self::refOptions('ref_role', 'ref_role');
    }

    /** @return array<int,array<string,mixed>> */
    public static function roleTypeOptions(): array
    {
        return self::refOptions('ref_role_type', 'ref_role_type');
    }

    /** Is this ref_status ID one of the allowed choices (ACTIVE / NOT_ACTIVE)? */
    public static function isValidStatus(int $statusId): bool
    {
        $status = config('app.status');
        return in_array($statusId, [$status['ACTIVE'], $status['NOT_ACTIVE']], true);
    }

    /**
     * Does an active row with this ID exist in a reference table?
     * Only the fixed names below are accepted.
     */
    public static function refExists(string $name, int $id): bool
    {
        $tables = [
            'department' => ['ref_department', 'ref_department_id'],
            'section'    => ['ref_section', 'ref_section_id'],
            'division'   => ['ref_division', 'ref_division_id'],
            'branch'     => ['ref_branch', 'ref_branch_id'],
            'role'       => ['ref_role', 'ref_role_id'],
            'role_type'  => ['ref_role_type', 'ref_role_type_id'],
        ];
        if (!isset($tables[$name])) {
            return false;
        }
        [$table, $pk] = $tables[$name];

        $stmt = db()->prepare("SELECT 1 FROM {$table} WHERE {$pk} = :id AND ref_status_id = :active LIMIT 1");
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /** Does this section belong to this department? */
    public static function sectionInDepartment(int $sectionId, int $departmentId): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_section WHERE ref_section_id = :section AND ref_department_id = :dept LIMIT 1'
        );
        $stmt->execute(['section' => $sectionId, 'dept' => $departmentId]);

        return $stmt->fetchColumn() !== false;
    }

    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE users_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Is this IC number already used? (excludeId lets an edit keep its own) */
    public static function myIdExists(string $myID, int $excludeId = 0): bool
    {
        $stmt = db()->prepare('SELECT 1 FROM users WHERE myID = :v AND users_id <> :id LIMIT 1');
        $stmt->execute(['v' => $myID, 'id' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /** Is this email already used? (excludeId lets an edit keep its own) */
    public static function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = db()->prepare('SELECT 1 FROM users WHERE email = :v AND users_id <> :id LIMIT 1');
        $stmt->execute(['v' => $email, 'id' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Insert an employee. Returns the new ID.
     *
     * @param array<string,mixed> $d Cleaned form values (see EmployeeController)
     */
    public static function create(array $d, string $passwordHash, ?int $userId): int
    {
        db()->prepare(
            'INSERT INTO users
                (myID, fullname, dob, age, email, phone_number, office_extension, password,
                 ref_department_id, ref_section_id, ref_division_id, ref_branch_id,
                 ref_role_id, ref_role_type_id, ref_status_id, created_by, updated_by)
             VALUES
                (:myID, :fullname, :dob, :age, :email, :phone, :ext, :password,
                 :department, :section, :division, :branch,
                 :role, :role_type, :status, :user, :user2)'
        )->execute([
            'myID'       => $d['myID'],
            'fullname'   => $d['fullname'],
            'dob'        => $d['dob'],
            'age'        => $d['age'],
            'email'      => $d['email'],
            'phone'      => $d['phone'],
            'ext'        => $d['ext'],
            'password'   => $passwordHash,
            'department' => $d['department'],
            'section'    => $d['section'],
            'division'   => $d['division'],
            'branch'     => $d['branch'],
            'role'       => $d['role'],
            'role_type'  => $d['role_type'],
            'status'     => $d['status'],
            'user'       => $userId,
            'user2'      => $userId,
        ]);

        return (int) db()->lastInsertId();
    }

    /**
     * Update an employee. The password is only changed when a new hash is given.
     *
     * @param array<string,mixed> $d Cleaned form values (see EmployeeController)
     */
    public static function update(int $id, array $d, ?string $passwordHash, ?int $userId): void
    {
        $sql = 'UPDATE users
                SET myID = :myID, fullname = :fullname, dob = :dob, age = :age,
                    email = :email, phone_number = :phone, office_extension = :ext,
                    ref_department_id = :department, ref_section_id = :section,
                    ref_division_id = :division, ref_branch_id = :branch,
                    ref_role_id = :role, ref_role_type_id = :role_type,
                    ref_status_id = :status, updated_by = :user';
        $params = [
            'myID'       => $d['myID'],
            'fullname'   => $d['fullname'],
            'dob'        => $d['dob'],
            'age'        => $d['age'],
            'email'      => $d['email'],
            'phone'      => $d['phone'],
            'ext'        => $d['ext'],
            'department' => $d['department'],
            'section'    => $d['section'],
            'division'   => $d['division'],
            'branch'     => $d['branch'],
            'role'       => $d['role'],
            'role_type'  => $d['role_type'],
            'status'     => $d['status'],
            'user'       => $userId,
            'id'         => $id,
        ];

        if ($passwordHash !== null) {
            $sql .= ', password = :password';
            $params['password'] = $passwordHash;
        }

        db()->prepare($sql . ' WHERE users_id = :id')->execute($params);
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
