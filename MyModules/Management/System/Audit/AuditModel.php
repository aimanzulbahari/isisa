<?php
declare(strict_types=1);

/**
 * AuditModel: database queries for the Audit Trail page
 * (MyModules/Management/System/Audit/Audit.php).
 *
 * Read only: the audit log is never changed from this page.
 * Related tables: audits, users.
 */
final class AuditModel
{
    /**
     * WHERE part shared by search() and count().
     *
     * @param  array{q?:string,event?:string,type?:string,from?:string,to?:string} $f Filters
     * @return array{0:string,1:array<string,mixed>} [sql, params]
     */
    private static function where(array $f): array
    {
        $sql    = ' WHERE 1 = 1';
        $params = [];

        $q = (string) ($f['q'] ?? '');
        if ($q !== '') {
            // Escape % and _ so they are searched as normal characters
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $sql .= ' AND (a.event LIKE :q1 OR a.auditable_type LIKE :q2 OR u.fullname LIKE :q3
                           OR u.myID LIKE :q4 OR a.ip_address LIKE :q5 OR CAST(a.auditable_id AS CHAR) LIKE :q6)';
            foreach (['q1', 'q2', 'q3', 'q4', 'q5', 'q6'] as $name) {
                $params[$name] = $like;
            }
        }
        if (($f['event'] ?? '') !== '') {
            $sql .= ' AND a.event = :event';
            $params['event'] = $f['event'];
        }
        if (($f['type'] ?? '') !== '') {
            $sql .= ' AND a.auditable_type = :type';
            $params['type'] = $f['type'];
        }
        if (($f['from'] ?? '') !== '') {
            $sql .= ' AND a.created_at >= :from';
            $params['from'] = $f['from'] . ' 00:00:00';
        }
        if (($f['to'] ?? '') !== '') {
            $sql .= ' AND a.created_at < DATE_ADD(:to, INTERVAL 1 DAY)';
            $params['to'] = $f['to'];
        }
        return [$sql, $params];
    }

    /** @param array<string,string> $filters */
    public static function count(array $filters = []): int
    {
        [$where, $params] = self::where($filters);

        $stmt = db()->prepare(
            'SELECT COUNT(*) FROM audits a LEFT JOIN users u ON u.users_id = a.user_id' . $where
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of audit rows, newest first.
     *
     * @param  array<string,string> $filters
     * @return array<int,array<string,mixed>>
     */
    public static function search(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = self::where($filters);

        $stmt = db()->prepare(
            'SELECT a.audit_id, a.user_id, a.event, a.auditable_type, a.auditable_id,
                    a.old_values, a.new_values, a.url, a.ip_address, a.user_agent, a.created_at,
                    u.fullname, u.myID
             FROM audits a
             LEFT JOIN users u ON u.users_id = a.user_id'
            . $where .
            ' ORDER BY a.audit_id DESC
             LIMIT :limit OFFSET :offset'
        );
        foreach ($params as $name => $value) {
            $stmt->bindValue($name, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Numbers for the 4 statistic cards.
     *
     * @return array{total:int,today:int,users_today:int,last_activity:?string}
     */
    public static function stats(): array
    {
        $row = db()->query(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(created_at >= CURDATE()), 0) AS today,
                    COUNT(DISTINCT CASE WHEN created_at >= CURDATE() THEN user_id END) AS users_today,
                    MAX(created_at) AS last_activity
             FROM audits'
        )->fetch() ?: [];

        return [
            'total'         => (int) ($row['total'] ?? 0),
            'today'         => (int) ($row['today'] ?? 0),
            'users_today'   => (int) ($row['users_today'] ?? 0),
            'last_activity' => $row['last_activity'] ?? null,
        ];
    }

    /**
     * Distinct values of a column, for the filter dropdowns.
     *
     * @param  'event'|'auditable_type' $column
     * @return string[]
     */
    public static function distinct(string $column): array
    {
        // The column name cannot be a bound value, so only these two are accepted
        if (!in_array($column, ['event', 'auditable_type'], true)) {
            return [];
        }
        return db()->query("SELECT DISTINCT {$column} FROM audits ORDER BY {$column} ASC")->fetchAll(PDO::FETCH_COLUMN);
    }
}
