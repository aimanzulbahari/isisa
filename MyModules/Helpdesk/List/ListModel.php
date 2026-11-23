<?php
declare(strict_types=1);

/**
 * HelpdeskListModel: database queries for the Helpdesk list page
 * (MyModules/Helpdesk/List/List.php).
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: helpdesk, ref_helpdesk_type, ref_severity, ref_status, users.
 *
 * Who may see a ticket: a normal user only sees the tickets he / she created.
 * Admin and Superadmin see all tickets. Every query that reads tickets takes a
 * $userId for this: pass null to see everything, or the user's ID to see only theirs.
 */
final class HelpdeskListModel
{
    /** Columns saved in the audit log (a snapshot of a ticket). */
    public const AUDIT_FIELDS = [
        'helpdesk_reference_no', 'helpdesk_type_id', 'helpdesk_severity_id',
        'helpdesk_title', 'helpdesk_description', 'helpdesk_attachment', 'status_id',
    ];

    /** The ticket statuses (ref_status IDs) that a ticket can have. */
    public static function ticketStatusIds(): array
    {
        $s = config('app.status');

        return [
            $s['TICKET_OPEN'], $s['TICKET_IN_PROGRESS'], $s['TICKET_ON_HOLD'],
            $s['TICKET_TRANSFERRED'], $s['TICKET_CLOSED'],
        ];
    }

    /**
     * WHERE part shared by search() and count().
     *
     * @return array{0:string,1:array<string,mixed>} [sql, params]
     */
    private static function where(string $q, int $statusId, ?int $userId): array
    {
        $sql    = ' WHERE 1 = 1';
        $params = [];

        if ($userId !== null) {
            $sql .= ' AND h.created_by = :user';
            $params['user'] = $userId;
        }
        if ($q !== '') {
            // Escape % and _ so they are searched as normal characters
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $sql .= ' AND (h.helpdesk_reference_no LIKE :q1 OR h.helpdesk_title LIKE :q2)';
            $params['q1'] = $like;
            $params['q2'] = $like;
        }
        if ($statusId > 0) {
            $sql .= ' AND h.status_id = :status';
            $params['status'] = $statusId;
        }
        return [$sql, $params];
    }

    /** Number of tickets that match the search / filter (for pagination). */
    public static function count(string $q, int $statusId, ?int $userId): int
    {
        [$where, $params] = self::where($q, $statusId, $userId);

        $stmt = db()->prepare('SELECT COUNT(*) FROM helpdesk h' . $where);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of tickets, newest first.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function search(string $q, int $statusId, ?int $userId, int $limit, int $offset): array
    {
        [$where, $params] = self::where($q, $statusId, $userId);

        $stmt = db()->prepare(
            'SELECT h.helpdesk_id, h.helpdesk_reference_no, h.helpdesk_title, h.helpdesk_description,
                    h.helpdesk_attachment, h.helpdesk_type_id, h.helpdesk_severity_id, h.status_id,
                    h.created_at, h.updated_at,
                    t.ref_helpdesk_type_description AS type_description,
                    sv.ref_severity_code            AS severity_code,
                    sv.ref_severity_description     AS severity_description,
                    st.ref_status_description       AS status_description,
                    u.fullname                      AS created_by_name
             FROM helpdesk h
             LEFT JOIN ref_helpdesk_type t ON t.ref_helpdesk_type_id = h.helpdesk_type_id
             LEFT JOIN ref_severity sv     ON sv.ref_severity_id     = h.helpdesk_severity_id
             LEFT JOIN ref_status st       ON st.ref_status_id       = h.status_id
             LEFT JOIN users u             ON u.users_id             = h.created_by'
            . $where .
            ' ORDER BY h.helpdesk_id DESC
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
     * One ticket (for the attachment download). Null when it does not exist,
     * or when $userId is given and the ticket belongs to someone else.
     *
     * @return array<string,mixed>|null
     */
    public static function findById(int $id, ?int $userId): ?array
    {
        $sql    = 'SELECT helpdesk_id, helpdesk_reference_no, helpdesk_attachment, created_by
                   FROM helpdesk WHERE helpdesk_id = :id';
        $params = ['id' => $id];

        if ($userId !== null) {
            $sql .= ' AND created_by = :user';
            $params['user'] = $userId;
        }
        $stmt = db()->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return $stmt->fetch() ?: null;
    }

    /**
     * Numbers for the 4 statistic cards.
     *
     * @return array{total:int,open:int,in_progress:int,closed:int}
     */
    public static function stats(?int $userId): array
    {
        $s      = config('app.status');
        $params = [
            'open' => $s['TICKET_OPEN'], 'progress' => $s['TICKET_IN_PROGRESS'], 'closed' => $s['TICKET_CLOSED'],
        ];
        $sql = 'SELECT COUNT(*) AS total,
                       COALESCE(SUM(status_id = :open), 0)     AS open_count,
                       COALESCE(SUM(status_id = :progress), 0) AS progress_count,
                       COALESCE(SUM(status_id = :closed), 0)   AS closed_count
                FROM helpdesk';
        if ($userId !== null) {
            $sql .= ' WHERE created_by = :user';
            $params['user'] = $userId;
        }
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch() ?: [];

        return [
            'total'       => (int) ($row['total'] ?? 0),
            'open'        => (int) ($row['open_count'] ?? 0),
            'in_progress' => (int) ($row['progress_count'] ?? 0),
            'closed'      => (int) ($row['closed_count'] ?? 0),
        ];
    }

    /**
     * Ticket statuses for the filter dropdown.
     *
     * @return array<int,array{ref_status_id:int,ref_status_description:string}>
     */
    public static function statusOptions(): array
    {
        $ids = self::ticketStatusIds();
        $in  = implode(',', array_map('intval', $ids)); // IDs come from config, not from the user

        return db()->query(
            "SELECT ref_status_id, ref_status_description FROM ref_status
             WHERE ref_status_id IN ({$in}) ORDER BY ref_status_id"
        )->fetchAll();
    }

    /**
     * Active ticket types for the "Jenis Aduan" dropdown.
     *
     * @return array<int,array{ref_helpdesk_type_id:int,ref_helpdesk_type_description:string}>
     */
    public static function typeOptions(): array
    {
        $stmt = db()->prepare(
            'SELECT ref_helpdesk_type_id, ref_helpdesk_type_description
             FROM ref_helpdesk_type
             WHERE ref_status_id = :active
             ORDER BY ref_helpdesk_type_description ASC'
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchAll();
    }

    /**
     * Active severity levels for the "Tahap Keterukan" choices (Low to Critical).
     *
     * @return array<int,array{ref_severity_id:int,ref_severity_code:string,ref_severity_description:string}>
     */
    public static function severityOptions(): array
    {
        $stmt = db()->prepare(
            'SELECT ref_severity_id, ref_severity_code, ref_severity_description
             FROM ref_severity
             WHERE ref_status_id = :active
             ORDER BY ref_severity_id ASC'
        );
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchAll();
    }

    /** Is this an active ticket type? */
    public static function typeExists(int $id): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_helpdesk_type WHERE ref_helpdesk_type_id = :id AND ref_status_id = :active LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /** Is this an active severity level? */
    public static function severityExists(int $id): bool
    {
        $stmt = db()->prepare(
            'SELECT 1 FROM ref_severity WHERE ref_severity_id = :id AND ref_status_id = :active LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'active' => config('app.status')['ACTIVE']]);

        return $stmt->fetchColumn() !== false;
    }

    /**
     * Insert a ticket. Returns the new ID.
     *
     * @param array<string,mixed> $data Keys from AUDIT_FIELDS
     */
    public static function create(array $data, int $userId): int
    {
        db()->prepare(
            'INSERT INTO helpdesk
                (helpdesk_reference_no, helpdesk_type_id, helpdesk_severity_id, helpdesk_title,
                 helpdesk_description, helpdesk_attachment, status_id, created_by, updated_by)
             VALUES
                (:ref, :type, :severity, :title, :description, :attachment, :status, :user, :user2)'
        )->execute([
            'ref'         => $data['helpdesk_reference_no'],
            'type'        => $data['helpdesk_type_id'],
            'severity'    => $data['helpdesk_severity_id'],
            'title'       => $data['helpdesk_title'],
            'description' => $data['helpdesk_description'],
            'attachment'  => $data['helpdesk_attachment'],
            'status'      => $data['status_id'],
            'user'        => $userId,
            'user2'       => $userId,
        ]);

        return (int) db()->lastInsertId();
    }
}
