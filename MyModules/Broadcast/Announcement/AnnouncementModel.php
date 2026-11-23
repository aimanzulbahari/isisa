<?php
declare(strict_types=1);

/**
 * AnnouncementModel: database queries for the Announcement page
 * (MyModules/Broadcast/Announcement/Announcement.php).
 *
 * Search, filter and paging are done here (LIKE / LIMIT), not in the browser.
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: announcement, ref_announcement_type, ref_status.
 */
final class AnnouncementModel
{
    /** Columns the user can edit (written by create() and update()). */
    private const FIELDS = [
        'announcement_title', 'announcement_type_id', 'announcement_description',
        'announcement_start_date', 'announcement_end_date', 'announcement_status_id',
    ];

    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = self::FIELDS;

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
            $sql .= ' AND (a.announcement_title LIKE :q1 OR a.announcement_description LIKE :q2 OR t.ref_announcement_type_description LIKE :q3)';
            $params['q1'] = $like;
            $params['q2'] = $like;
            $params['q3'] = $like;
        }
        if ($statusId > 0) {
            $sql .= ' AND a.announcement_status_id = :status';
            $params['status'] = $statusId;
        }
        return [$sql, $params];
    }

    /** Number of rows that match the search / filter (for pagination). */
    public static function count(string $q = '', int $statusId = 0): int
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare(
            'SELECT COUNT(*)
             FROM announcement a
             JOIN ref_announcement_type t ON t.ref_announcement_type_id = a.announcement_type_id'
            . $where
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of announcements, newest first, with the type and status text.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function search(string $q, int $statusId, int $limit, int $offset): array
    {
        [$where, $params] = self::where($q, $statusId);

        $stmt = db()->prepare(
            'SELECT a.*, t.ref_announcement_type_description AS type_description,
                    s.ref_status_description AS status_description
             FROM announcement a
             JOIN ref_announcement_type t ON t.ref_announcement_type_id = a.announcement_type_id
             JOIN ref_status s            ON s.ref_status_id = a.announcement_status_id'
            . $where .
            ' ORDER BY a.announcement_start_date DESC, a.announcement_id DESC
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
     * Announcements that are shown to visitors right now (the carousel on the login page):
     * status ACTIVE, the start date has come and the end date has not passed (empty end date = no end).
     * Newest first. Nothing is returned if the table cannot be read, so the login page never breaks.
     *
     * @return array<int,array{announcement_id:int,announcement_title:string,announcement_description:?string,announcement_start_date:string,type_description:string}>
     */
    public static function published(int $limit = 5): array
    {
        try {
            $now  = date('Y-m-d H:i:s');
            $stmt = db()->prepare(
                'SELECT a.announcement_id, a.announcement_title, a.announcement_description,
                        a.announcement_start_date, t.ref_announcement_type_description AS type_description
                 FROM announcement a
                 JOIN ref_announcement_type t ON t.ref_announcement_type_id = a.announcement_type_id
                 WHERE a.announcement_status_id = :active
                   AND a.announcement_start_date <= :now_start
                   AND (a.announcement_end_date IS NULL OR a.announcement_end_date >= :now_end)
                 ORDER BY a.announcement_start_date DESC, a.announcement_id DESC
                 LIMIT :limit'
            );
            $stmt->bindValue('active', config('app.status')['ACTIVE'], PDO::PARAM_INT);
            $stmt->bindValue('now_start', $now);
            $stmt->bindValue('now_end', $now);
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll();
        } catch (Throwable $e) {
            error_log('[ANNOUNCEMENT] Could not read the published announcements: ' . $e->getMessage());
            return [];
        }
    }

    /** One announcement, or null when it does not exist. */
    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM announcement WHERE announcement_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Numbers for the cards at the top of the page.
     *
     * @return array{total:int,active:int,not_active:int,last_update:?string}
     */
    public static function stats(): array
    {
        $status = config('app.status');
        $stmt   = db()->prepare(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(announcement_status_id = :active), 0)     AS active,
                    COALESCE(SUM(announcement_status_id = :not_active), 0) AS not_active,
                    MAX(updated_at) AS last_update
             FROM announcement'
        );
        $stmt->execute(['active' => $status['ACTIVE'], 'not_active' => $status['NOT_ACTIVE']]);
        $row = $stmt->fetch();

        return [
            'total'       => (int) $row['total'],
            'active'      => (int) $row['active'],
            'not_active'  => (int) $row['not_active'],
            'last_update' => $row['last_update'],
        ];
    }

    /**
     * Active announcement types for the "Jenis" dropdown.
     *
     * @return array<int,array{ref_announcement_type_id:int,ref_announcement_type_description:string}>
     */
    public static function typeOptions(): array
    {
        $stmt = db()->prepare(
            'SELECT ref_announcement_type_id, ref_announcement_type_description
             FROM ref_announcement_type
             WHERE ref_status_id = :active
             ORDER BY ref_announcement_type_description ASC'
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchAll();
    }

    /** Is this an active announcement type? */
    public static function typeExists(int $id): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_announcement_type WHERE ref_announcement_type_id = :id AND ref_status_id = :active LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * The statuses an announcement can have: only ACTIVE and NOT_ACTIVE.
     *
     * @return array<int,array{ref_status_id:int,ref_status_code:string,ref_status_description:string}>
     */
    public static function statusOptions(): array
    {
        $status = config('app.status');
        $stmt   = db()->prepare(
            'SELECT ref_status_id, ref_status_code, ref_status_description
             FROM ref_status
             WHERE ref_status_id IN (:active, :not_active)
             ORDER BY ref_status_id ASC'
        );
        $stmt->execute(['active' => $status['ACTIVE'], 'not_active' => $status['NOT_ACTIVE']]);

        return $stmt->fetchAll();
    }

    /**
     * Insert a new announcement. Returns its ID.
     *
     * @param array<string,mixed> $data Keys from FIELDS
     */
    public static function create(array $data, ?int $userId): int
    {
        $columns = self::FIELDS;
        $sql = 'INSERT INTO announcement (' . implode(', ', $columns) . ', created_by, updated_by)
                VALUES (' . implode(', ', array_map(static fn ($c) => ':' . $c, $columns)) . ', :created_by, :updated_by)';

        $params = ['created_by' => $userId, 'updated_by' => $userId];
        foreach ($columns as $c) {
            $params[$c] = $data[$c] ?? null;
        }
        db()->prepare($sql)->execute($params);

        return (int) db()->lastInsertId();
    }

    /**
     * Update an announcement.
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
            'UPDATE announcement SET ' . implode(', ', $sets) . ', updated_by = :updated_by WHERE announcement_id = :id'
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
