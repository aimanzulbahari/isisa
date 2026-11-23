<?php
declare(strict_types=1);

/**
 * BackupDataModel: database queries and the SQL dump for the Backup Data page
 * (MyModules/Management/System/Backup-Data/Backup-Data.php).
 *
 * Related tables: data_backup, ref_status, users.
 * The dump is written in plain PHP (PDO), so it does not need mysqldump.
 * No sessions, redirects or HTML here.
 */
final class BackupDataModel
{
    /** Columns the user can edit (written by create() and update()). */
    private const FIELDS = [
        'data_backup_code', 'data_backup_name', 'file_path', 'file_size', 'status_id',
    ];

    /** Columns saved in the audit log (a snapshot of a row). */
    private const AUDIT_FIELDS = self::FIELDS;

    /** Rows read from a table at one time while dumping (keeps the memory use low). */
    private const DUMP_CHUNK = 500;

    /** Tables whose rows are NOT worth backing up (only the structure is saved). */
    private const STRUCTURE_ONLY = ['sessions'];

    // ------------------------------------------------------------------
    // Queries for the page
    // ------------------------------------------------------------------

    /**
     * WHERE part and the values for the search / status filter.
     *
     * @param  array<string,string> $filters q, status (success | failed)
     * @return array{0:string,1:array<string,mixed>}
     */
    private static function where(array $filters): array
    {
        $where  = [];
        $params = [];

        if (isset($filters['q'])) {
            $where[]           = '(b.data_backup_code LIKE :q1 OR b.data_backup_name LIKE :q2 OR u.fullname LIKE :q3)';
            $like              = '%' . addcslashes($filters['q'], '%_\\') . '%';
            $params['q1']      = $like;
            $params['q2']      = $like;
            $params['q3']      = $like;
        }
        if (isset($filters['status'])) {
            $where[]          = 'b.status_id = :status';
            $params['status'] = config('app.status')[$filters['status'] === 'failed' ? 'FAILED' : 'SUCCESS'];
        }

        return [$where === [] ? '' : 'WHERE ' . implode(' AND ', $where), $params];
    }

    /** @param array<string,string> $filters */
    public static function count(array $filters = []): int
    {
        [$where, $params] = self::where($filters);
        $stmt = db()->prepare(
            "SELECT COUNT(*) FROM data_backup b LEFT JOIN users u ON u.users_id = b.created_by {$where}"
        );
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * One page of backups, newest first.
     *
     * @param  array<string,string> $filters
     * @return array<int,array<string,mixed>>
     */
    public static function search(array $filters, int $limit, int $offset): array
    {
        [$where, $params] = self::where($filters);
        $stmt = db()->prepare(
            "SELECT b.*, s.ref_status_code, s.ref_status_description, u.fullname AS created_by_name
             FROM data_backup b
             LEFT JOIN ref_status s ON s.ref_status_id = b.status_id
             LEFT JOIN users u ON u.users_id = b.created_by
             {$where}
             ORDER BY b.created_at DESC, b.data_backup_id DESC
             LIMIT {$limit} OFFSET {$offset}"
        );
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** Numbers for the four cards. */
    public static function stats(): array
    {
        $status = config('app.status');
        $stmt   = db()->prepare(
            'SELECT COUNT(*) AS total,
                    COALESCE(SUM(status_id = :failed), 0)  AS failed,
                    COALESCE(SUM(status_id = :success), 0) AS success,
                    MAX(CASE WHEN status_id = :success2 THEN created_at END) AS last_backup
             FROM data_backup'
        );
        $stmt->execute([
            'failed'   => $status['FAILED'],
            'success'  => $status['SUCCESS'],
            'success2' => $status['SUCCESS'],
        ]);
        $row = $stmt->fetch();

        return [
            'total'       => (int) $row['total'],
            'failed'      => (int) $row['failed'],
            'success'     => (int) $row['success'],
            'last_backup' => $row['last_backup'] ?: null,
        ];
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM data_backup WHERE data_backup_id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    /**
     * Insert a backup record. Returns its ID.
     *
     * @param array<string,mixed> $data Keys from FIELDS
     */
    public static function create(array $data, ?int $userId): int
    {
        $columns = self::FIELDS;
        $sql = 'INSERT INTO data_backup (' . implode(', ', $columns) . ', created_by, updated_by)
                VALUES (' . implode(', ', array_map(static fn ($c) => ':' . $c, $columns)) . ', :created_by, :updated_by)';

        $params = ['created_by' => $userId, 'updated_by' => $userId];
        foreach ($columns as $c) {
            $params[$c] = $data[$c] ?? null;
        }
        db()->prepare($sql)->execute($params);

        return (int) db()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        db()->prepare('DELETE FROM data_backup WHERE data_backup_id = :id')->execute(['id' => $id]);
    }

    /** True if a backup with this code already exists. */
    public static function codeExists(string $code): bool
    {
        $stmt = db()->prepare('SELECT 1 FROM data_backup WHERE data_backup_code = :code LIMIT 1');
        $stmt->execute(['code' => $code]);

        return (bool) $stmt->fetchColumn();
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

    // ------------------------------------------------------------------
    // The SQL dump
    // ------------------------------------------------------------------

    /**
     * Write a full SQL dump (structure + data of every table) of the current database to $path.
     * Throws RuntimeException / PDOException if anything goes wrong.
     *
     * @return int Size of the file in bytes
     */
    public static function dumpTo(string $path): int
    {
        $pdo    = db();
        $dbName = (string) config('database.database');

        $handle = @fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException('Tidak dapat menulis fail sandaran.');
        }

        try {
            self::write($handle, "-- ISISA database backup\n"
                . "-- Database : {$dbName}\n"
                . '-- Created  : ' . date('Y-m-d H:i:s') . "\n\n"
                . "SET NAMES utf8mb4;\n"
                . "SET FOREIGN_KEY_CHECKS = 0;\n"
                . "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $table = (string) $table;
                $quoted = '`' . str_replace('`', '``', $table) . '`';

                $create = $pdo->query("SHOW CREATE TABLE {$quoted}")->fetch(PDO::FETCH_NUM);
                self::write($handle, "-- ----------------------------\n-- Table: {$table}\n-- ----------------------------\n"
                    . "DROP TABLE IF EXISTS {$quoted};\n" . $create[1] . ";\n\n");

                if (!in_array($table, self::STRUCTURE_ONLY, true)) {
                    self::dumpRows($handle, $table, $quoted);
                }
            }

            self::write($handle, "SET FOREIGN_KEY_CHECKS = 1;\n");
        } finally {
            fclose($handle);
        }

        clearstatcache(true, $path);

        return (int) filesize($path);
    }

    /** @param resource $handle */
    private static function dumpRows($handle, string $table, string $quoted): void
    {
        $pdo    = db();
        $offset = 0;

        do {
            $rows = $pdo->query('SELECT * FROM ' . $quoted . ' LIMIT ' . self::DUMP_CHUNK . ' OFFSET ' . $offset)
                ->fetchAll(PDO::FETCH_ASSOC);
            if ($rows === []) {
                break;
            }

            $columns = implode(', ', array_map(
                static fn (string $c): string => '`' . str_replace('`', '``', $c) . '`',
                array_keys($rows[0])
            ));
            $values = [];
            foreach ($rows as $row) {
                $values[] = '(' . implode(', ', array_map(
                    static fn ($v): string => $v === null ? 'NULL' : $pdo->quote((string) $v),
                    $row
                )) . ')';
            }
            self::write($handle, "INSERT INTO {$quoted} ({$columns}) VALUES\n" . implode(",\n", $values) . ";\n");

            $offset += self::DUMP_CHUNK;
        } while (count($rows) === self::DUMP_CHUNK);

        self::write($handle, "\n");
    }

    /** @param resource $handle */
    private static function write($handle, string $text): void
    {
        if (fwrite($handle, $text) === false) {
            throw new RuntimeException('Gagal menulis fail sandaran (ruang cakera penuh?).');
        }
    }
}
