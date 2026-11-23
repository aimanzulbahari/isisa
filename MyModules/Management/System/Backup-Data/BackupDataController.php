<?php
declare(strict_types=1);

/**
 * BackupDataController: the logic of the Backup Data page (Backup-Data.php).
 *
 * Backup-Data.php calls handleBackupData() once. The function:
 *   - only lets ADMIN / SUPERADMIN in (anyone else gets 403 and the attempt is audited)
 *   - GET ?download=ID : sends the backup file to the browser
 *   - POST action=create : makes a new SQL backup in storage/backups and records it
 *   - POST action=delete : deletes the file and the record
 *   - normal page view: returns the cards, one page of the list and the messages
 *     (search, filter and page come from the URL: ?q=&status=&page=)
 *   - every POST ends with a redirect (Post/Redirect/Get). CSRF is checked by core/includes/csrf.php.
 *
 * Files are saved as storage/backups/<code>.sql. The code is made by the system
 * (BKP-YYYYMMDD-HHMMSS), the file name never comes from the user.
 * A backup that fails is still recorded (status FAILED, no file) so the "Backup Gagal" card is right.
 *
 * Audit log (table: data_backup, auditable_type = data_backup):
 *   CREATED        a backup was made
 *   BACKUP_FAILED  making a backup failed
 *   DOWNLOADED     a backup file was downloaded
 *   DELETED        a backup was deleted
 *   INVALID_ACTION an unknown action was posted
 *   ACCESS_DENIED  a user without the right role tried to open the page
 * audits.auditable_id is required, so events without a record use 0.
 */

require_once __DIR__ . '/BackupDataModel.php';

defined('BACKUP_PATH') || define('BACKUP_PATH', STORAGE_PATH . '/backups');

const BACKUP_TABLE    = 'data_backup';
const BACKUP_PER_PAGE = 10;
const BACKUP_NAME_MAX = 255;   // same as maxlength in the form

if (!function_exists('backupBack')) {
    /** Save a message then go back to the page. */
    function backupBack(string $type, string $message, ?string $name = null): never
    {
        setFlash($type, $message);

        if ($name !== null) {
            $_SESSION['backup_old_name'] = $name;
        }
        redirect(routeUrl('system-backup'), 303);
    }
}

if (!function_exists('backupEnsureDir')) {
    /** Make sure storage/backups exists, is writable and cannot be opened from a browser. */
    function backupEnsureDir(): void
    {
        if (!is_dir(BACKUP_PATH) && !@mkdir(BACKUP_PATH, 0775, true) && !is_dir(BACKUP_PATH)) {
            throw new RuntimeException('Folder storage/backups tidak dapat dicipta.');
        }
        if (!is_writable(BACKUP_PATH)) {
            throw new RuntimeException('Folder storage/backups tidak boleh ditulis.');
        }
        // Second layer of protection (the root .htaccess already blocks /storage)
        $htaccess = BACKUP_PATH . '/.htaccess';
        if (!is_file($htaccess)) {
            @file_put_contents($htaccess, "Require all denied\n");
        }
    }
}

if (!function_exists('backupFilePath')) {
    /**
     * The full path of a backup file, or null if the record has no usable file.
     * The path must stay inside storage/backups (never trust the stored path blindly).
     */
    function backupFilePath(array $row): ?string
    {
        $file = basename((string) $row['file_path']);
        if ($file === '' || !str_ends_with($file, '.sql')) {
            return null;
        }
        $full = realpath(BACKUP_PATH . '/' . $file);
        $base = realpath(BACKUP_PATH);

        return ($full !== false && $base !== false && str_starts_with($full, $base . DIRECTORY_SEPARATOR) && is_file($full))
            ? $full
            : null;
    }
}

if (!function_exists('backupCreate')) {
    /** Handle POST action=create. */
    function backupCreate(): never
    {
        $name = $_POST['backup_name'] ?? '';
        $name = is_string($name) ? trim((string) preg_replace('/\s+/u', ' ', $name)) : '';

        if ($name === '') {
            backupBack('error', 'Sila masukkan nama sandaran.', $name);
        }
        if (mb_strlen($name) > BACKUP_NAME_MAX) {
            backupBack('error', 'Nama sandaran terlalu panjang (maksimum ' . BACKUP_NAME_MAX . ' aksara).', $name);
        }

        $status = config('app.status');
        $userId = currentUserId();

        // Unique code made by the system: the file name comes from it
        $code = 'BKP-' . date('Ymd-His');
        for ($i = 2; BackupDataModel::codeExists($code); $i++) {
            $code = 'BKP-' . date('Ymd-His') . '-' . $i;
        }
        $file = $code . '.sql';
        $path = BACKUP_PATH . '/' . $file;

        try {
            @set_time_limit(0);
            backupEnsureDir();
            $size = BackupDataModel::dumpTo($path);
        } catch (Throwable $e) {
            error_log('[BACKUP] ' . $e->getMessage());
            if (is_file($path)) {
                @unlink($path); // never keep a half-written backup
            }

            $failed = [
                'data_backup_code' => $code,
                'data_backup_name' => $name,
                'file_path'        => '',
                'file_size'        => 0,
                'status_id'        => $status['FAILED'],
            ];
            try {
                $failedId = BackupDataModel::create($failed, $userId);
            } catch (PDOException $db) {
                error_log('[BACKUP] ' . $db->getMessage());
                $failedId = 0;
            }
            audit('BACKUP_FAILED', BACKUP_TABLE, $failedId, null, $failed + ['reason' => $e->getMessage()]);
            backupBack('error', 'Sandaran gagal dibuat. Sila cuba lagi.', $name);
        }

        $new = [
            'data_backup_code' => $code,
            'data_backup_name' => $name,
            'file_path'        => $file,
            'file_size'        => $size,
            'status_id'        => $status['SUCCESS'],
        ];
        try {
            $id = BackupDataModel::create($new, $userId);
        } catch (PDOException $e) {
            error_log('[BACKUP] ' . $e->getMessage());
            @unlink($path); // no record = no orphan file
            audit('BACKUP_FAILED', BACKUP_TABLE, 0, null, $new + ['reason' => 'Ralat pangkalan data']);
            backupBack('error', 'Ralat pangkalan data. Sila cuba lagi.', $name);
        }

        audit('CREATED', BACKUP_TABLE, $id, null, $new);
        backupBack('success', 'Sandaran data berjaya dibuat.');
    }
}

if (!function_exists('backupDelete')) {
    /** Handle POST action=delete. */
    function backupDelete(): never
    {
        $id  = toInt($_POST['id'] ?? 0, 0);
        $row = $id > 0 ? BackupDataModel::find($id) : null;
        if ($row === null) {
            audit('INVALID_ACTION', BACKUP_TABLE, 0, null, ['action' => 'delete', 'id' => $id]);
            backupBack('error', 'Rekod sandaran tidak dijumpai.');
        }

        $path = backupFilePath($row);
        if ($path !== null && !@unlink($path)) {
            backupBack('error', 'Fail sandaran tidak dapat dipadam.');
        }

        try {
            BackupDataModel::delete($id);
        } catch (PDOException $e) {
            error_log('[BACKUP] ' . $e->getMessage());
            backupBack('error', 'Ralat pangkalan data. Sila cuba lagi.');
        }

        audit('DELETED', BACKUP_TABLE, $id, BackupDataModel::auditSnapshot($row), null);
        backupBack('success', 'Sandaran berjaya dipadam.');
    }
}

if (!function_exists('backupDownload')) {
    /** Handle GET ?download=ID: send the file. */
    function backupDownload(int $id): never
    {
        $row  = $id > 0 ? BackupDataModel::find($id) : null;
        $path = $row !== null ? backupFilePath($row) : null;
        if ($row === null || $path === null) {
            backupBack('error', 'Fail sandaran tidak dijumpai.');
        }

        audit('DOWNLOADED', BACKUP_TABLE, $id, null, ['data_backup_code' => $row['data_backup_code']]);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/sql; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Length: ' . (string) filesize($path));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        readfile($path);
        exit;
    }
}

if (!function_exists('backupFilters')) {
    /**
     * The filters in the URL, cleaned. Empty filters are left out.
     *
     * @param  array<string,mixed> $source $_GET
     * @return array<string,string>
     */
    function backupFilters(array $source): array
    {
        $filters = [];

        $q = trim((string) ($source['q'] ?? ''));
        if ($q !== '') {
            $filters['q'] = mb_substr($q, 0, 100);
        }
        $status = trim((string) ($source['status'] ?? ''));
        if (in_array($status, ['success', 'failed'], true)) {
            $filters['status'] = $status;
        }

        return $filters;
    }
}

if (!function_exists('handleBackupData')) {
    /**
     * Run the Backup Data page logic.
     *
     * @return array{
     *   rows: array<int,array<string,mixed>>,
     *   stats: array{total:int,failed:int,success:int,last_backup:?string},
     *   page: array<string,mixed>,
     *   filters: array<string,string>,
     *   old_name: string,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleBackupData(): array
    {
        // Only admins may make, download or delete backups
        requireAuth();
        if (!isAdmin()) {
            audit('ACCESS_DENIED', BACKUP_TABLE, 0, null, ['role' => currentRole(), 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']);
            respondError(403, 'You do not have permission to access this page.');
        }

        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            match ($_POST['action'] ?? '') {
                'create' => backupCreate(),
                'delete' => backupDelete(),
                default  => (static function (): never {
                    audit('INVALID_ACTION', BACKUP_TABLE, 0, null, ['action' => (string) ($_POST['action'] ?? '')]);
                    backupBack('error', 'Tindakan tidak sah.');
                })(),
            };
        }

        // ---------- Download ----------
        if (isset($_GET['download'])) {
            backupDownload(toInt($_GET['download'], 0));
        }

        // ---------- Normal page view ----------
        $oldName = (string) ($_SESSION['backup_old_name'] ?? '');
        unset($_SESSION['backup_old_name']);

        $filters = backupFilters($_GET);
        $page    = paginate(BackupDataModel::count($filters), currentPage(), BACKUP_PER_PAGE);

        return [
            'rows'     => BackupDataModel::search($filters, $page['per_page'], $page['offset']),
            'stats'    => BackupDataModel::stats(),
            'page'     => $page,
            'filters'  => $filters,
            'old_name' => $oldName,
            'flash'    => pullFlash(),
        ];
    }
}
