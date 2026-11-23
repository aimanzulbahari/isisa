<?php
declare(strict_types=1);

/**
 * BranchModel: database queries for the Branch page
 * (MyModules/Management/Wisma/Branch/Branch.php).
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: ref_branch, ref_status.
 */
final class BranchModel
{
    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = ['ref_branch_code', 'ref_branch_description', 'ref_status_id'];

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
            $sql .= ' AND (d.ref_branch_code LIKE :q1 OR d.ref_branch_description LIKE :q2)';
            $params['q1'] = $like;
            $params['q2'] = $like;
        }
        if ($statusId > 0) {
            $sql .= ' AND d.ref_status_id = :status';
            $params['status'] = $statusId;
        }
        return [$sql, $params];
    }

    /** Number of rows that match the search / filter (for pagination). */
    public static function count(string $q = '', int $statusId = 0): int
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare('SELECT COUNT(*) FROM ref_branch d' . $where);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of branchs.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function search(string $q, int $statusId, int $limit, int $offset): array
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare(
            'SELECT d.ref_branch_id, d.ref_branch_code, d.ref_branch_description,
                    d.ref_status_id, s.ref_status_description, d.created_at, d.updated_at
             FROM ref_branch d
             LEFT JOIN ref_status s ON s.ref_status_id = d.ref_status_id'
            . $where .
            ' ORDER BY d.ref_branch_id ASC
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
             FROM ref_branch'
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

    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM ref_branch WHERE ref_branch_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /** Is this code already used? (excludeId lets an edit keep its own code) */
    public static function codeExists(string $code, int $excludeId = 0): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_branch
             WHERE ref_branch_code = :code AND ref_branch_id <> :id
             LIMIT 1'
        );
        $stmt->execute(['code' => $code, 'id' => $excludeId]);

        return $stmt->fetchColumn() !== false;
    }

    /** Is this ref_status ID one of the allowed choices (ACTIVE / NOT_ACTIVE)? */
    public static function isValidStatus(int $statusId): bool
    {
        $status = config('app.status');
        return in_array($statusId, [$status['ACTIVE'], $status['NOT_ACTIVE']], true);
    }

    /** Insert a branch. Returns the new ID. */
    public static function create(string $code, string $description, int $statusId, ?int $userId): int
    {
        db()->prepare(
            'INSERT INTO ref_branch
                (ref_branch_code, ref_branch_description, ref_status_id, created_by, updated_by)
             VALUES
                (:code, :description, :status, :user, :user2)'
        )->execute([
            'code'        => $code,
            'description' => $description,
            'status'      => $statusId,
            'user'        => $userId,
            'user2'       => $userId,
        ]);

        return (int) db()->lastInsertId();
    }

    /** Update a branch. */
    public static function update(int $id, string $code, string $description, int $statusId, ?int $userId): void
    {
        db()->prepare(
            'UPDATE ref_branch
             SET ref_branch_code = :code,
                 ref_branch_description = :description,
                 ref_status_id = :status,
                 updated_by = :user
             WHERE ref_branch_id = :id'
        )->execute([
            'code'        => $code,
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
