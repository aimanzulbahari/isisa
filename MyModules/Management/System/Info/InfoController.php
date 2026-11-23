<?php
declare(strict_types=1);

/**
 * InfoController: the logic of the System Info page (Info.php).
 *
 * Info.php calls handleInfo() once. The function:
 *   - only lets ADMIN / SUPERADMIN in (anyone else gets 403 and the attempt is audited)
 *   - on a normal page view (GET): returns the saved settings, the numbers for the
 *     cards, the PHP information and the messages to show
 *   - on a form submit (POST): validates, saves the logo / agency logo files, saves the
 *     settings, writes the audit log, then ALWAYS redirects (Post/Redirect/Get)
 *
 * system_info holds ONE row. The first save inserts it, the next saves update it.
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits, auditable_type = system_info). Every action is recorded:
 *   CREATED        the settings were saved for the first time
 *   UPDATED        the settings were changed (only the changed fields are saved)
 *   NO_CHANGE      the form was saved without any change
 *   UPDATE_FAILED  saving was refused (validation / file problem / database error)
 *   ACCESS_DENIED  a user without the right role tried to open the page
 * audits.auditable_id is required, so a failed first save uses 0 (no row exists yet).
 */

require_once __DIR__ . '/InfoModel.php';

const INFO_TABLE        = 'system_info';
const INFO_UPLOAD_DIR   = 'uploads/system';            // inside /public, so the browser can show the images
const INFO_UPLOAD_MAX   = 2 * 1024 * 1024;             // 2 MB
const INFO_LOGO_TYPES   = ['png' => ['image/png'], 'jpg' => ['image/jpeg'], 'jpeg' => ['image/jpeg'], 'webp' => ['image/webp']];

if (!function_exists('infoBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the page.
     *
     * @param array<string,mixed>|null $old Typed values, to fill the form again
     */
    function infoBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['info_old'] = $old;
        }
        redirect(routeUrl('system-info'), 303);
    }
}

if (!function_exists('infoFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values
     */
    function infoFail(int $id, string $message, array $old): never
    {
        audit('UPDATE_FAILED', INFO_TABLE, $id, null, ['reason' => $message] + $old);
        infoBack('error', $message, $old);
    }
}

if (!function_exists('infoCheckUpload')) {
    /**
     * Check one uploaded file BEFORE anything is saved.
     * The type is read from the file content, so a renamed file is caught.
     *
     * @param  array<string,string[]> $types extension => allowed MIME types
     * @return string|null An error message, or null when the file is fine (or no file was chosen)
     */
    function infoCheckUpload(string $field, string $label, array $types): ?string
    {
        $file = $_FILES[$field] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $error = (int) $file['error'];
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return "{$label} terlalu besar (maksimum 2 MB).";
        }
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            return "{$label} gagal dimuat naik. Sila cuba lagi.";
        }
        if ((int) $file['size'] > INFO_UPLOAD_MAX) {
            return "{$label} terlalu besar (maksimum 2 MB).";
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!isset($types[$ext])) {
            return "{$label} mesti dalam format " . strtoupper(implode(', ', array_keys($types))) . '.';
        }

        $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if (!in_array($mime, $types[$ext], true)) {
            return "{$label} bukan fail imej yang sah.";
        }
        // ICO files are not read by getimagesize(), the rest must be real images
        if ($ext !== 'ico' && @getimagesize((string) $file['tmp_name']) === false) {
            return "{$label} bukan fail imej yang sah.";
        }
        return null;
    }
}

if (!function_exists('infoStoreUpload')) {
    /**
     * Save a file that passed infoCheckUpload().
     *
     * @return string|null Path inside /public (e.g. "uploads/system/logo-ab12.png"), or null if no file / save failed
     */
    function infoStoreUpload(string $field, string $prefix): ?string
    {
        $file = $_FILES[$field] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $dir = PUBLIC_PATH . '/' . INFO_UPLOAD_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            return null;
        }
        // Never run scripts from this folder, even if one got in
        $guard = $dir . '/.htaccess';
        if (!is_file($guard)) {
            file_put_contents($guard, "Options -ExecCGI -Indexes\n<FilesMatch \"\\.(php|phtml|phar)\$\">\n    Require all denied\n</FilesMatch>\n");
        }

        // Random name: the original name is never used
        $ext  = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $name = $prefix . '-' . bin2hex(random_bytes(8)) . '.' . $ext;

        return move_uploaded_file((string) $file['tmp_name'], $dir . '/' . $name) ? INFO_UPLOAD_DIR . '/' . $name : null;
    }
}

if (!function_exists('infoDeleteUpload')) {
    /** Remove an old logo / agency logo, but only if it is inside the upload folder. */
    function infoDeleteUpload(?string $path): void
    {
        if ($path === null || !str_starts_with($path, INFO_UPLOAD_DIR . '/') || str_contains($path, '..')) {
            return;
        }
        $file = PUBLIC_PATH . '/' . $path;
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

if (!function_exists('infoText')) {
    /** A text input: trimmed, spaces collapsed (line breaks kept for multi-line fields). */
    function infoText(string $key, bool $multiline = false): string
    {
        $value = trim((string) ($_POST[$key] ?? ''));

        return $multiline
            ? trim((string) preg_replace('/[ \t]+/u', ' ', str_replace("\r\n", "\n", $value)))
            : trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}

if (!function_exists('infoSave')) {
    /** Handle the form POST. */
    function infoSave(): never
    {
        $current = InfoModel::get();
        $id      = $current !== null ? (int) $current['system_info_id'] : 0;

        // 1. Read the typed values
        $typed = [
            'system_name'          => infoText('system_name'),
            'system_short_name'    => infoText('system_short_name'),
            'system_version'       => infoText('system_version'),
            'organization_name'    => infoText('organization_name'),
            'system_department'    => infoText('system_department'),
            'support_email'        => mb_strtolower(infoText('support_email')),
            'support_phone'        => infoText('support_phone'),
            'copyright_text'       => infoText('copyright_text'),
        ];

        // 2. Input check
        $rules = [
            ['system_name', 'Nama Sistem', 150, true],
            ['system_short_name', 'Akronim Sistem', 50, true],
            ['system_version', 'Versi Sistem', 20, true],
            ['organization_name', 'Nama Organisasi', 255, true],
            ['support_email', 'Emel', 150, true],
            ['support_phone', 'No. Telefon', 50, false],
            ['copyright_text', 'Teks Hak Cipta', 255, false],
        ];
        foreach ($rules as [$key, $label, $max, $required]) {
            if ($required && $typed[$key] === '') {
                infoFail($id, "Sila isi {$label}.", $typed);
            }
            if (mb_strlen($typed[$key]) > $max) {
                infoFail($id, "{$label} terlalu panjang (maksimum {$max} aksara).", $typed);
            }
        }
        if (!preg_match('/^\d+(\.\d+){0,3}([-+][0-9A-Za-z.\-]+)?$/', $typed['system_version'])) {
            infoFail($id, 'Versi Sistem tidak sah (cth: 1.0.0).', $typed);
        }
        if (filter_var($typed['support_email'], FILTER_VALIDATE_EMAIL) === false) {
            infoFail($id, 'Emel tidak sah.', $typed);
        }
        if ($typed['support_phone'] !== '' && !preg_match('/^[0-9+\-() ]{7,50}$/', $typed['support_phone'])) {
            infoFail($id, 'No. Telefon tidak sah (cth: 03-1234 5678).', $typed);
        }
        $department = null;
        if ($typed['system_department'] !== '') {
            $department = toInt($typed['system_department']);
            if ($department <= 0 || !InfoModel::departmentExists($department)) {
                infoFail($id, 'Jabatan yang dipilih tidak sah.', $typed);
            }
        }
        $typed['system_department'] = $department === null ? '' : (string) $department;

        foreach ([['system_logo', 'Logo Sistem', INFO_LOGO_TYPES], ['agency_logo', 'Logo Agensi', INFO_LOGO_TYPES]] as [$field, $label, $types]) {
            $error = infoCheckUpload($field, $label, $types);
            if ($error !== null) {
                infoFail($id, $error, $typed);
            }
        }

        // 3. Save the files, then the settings
        $new = [
            'system_name'          => $typed['system_name'],
            'system_short_name'    => $typed['system_short_name'],
            'system_version'       => $typed['system_version'],
            'organization_name'    => $typed['organization_name'],
            'system_department'    => $department,
            'support_email'        => $typed['support_email'],
            'support_phone'        => $typed['support_phone'] !== '' ? $typed['support_phone'] : null,
            'logo_path'            => $current['logo_path'] ?? null,
            'agency_logo_path'     => $current['agency_logo_path'] ?? null,
            'copyright_text'       => $typed['copyright_text'] !== '' ? $typed['copyright_text'] : null,
        ];

        $saved = [];   // files saved now (removed again if the database save fails)
        $old   = [];   // files that are replaced (removed after the database save works)
        foreach ([['system_logo', 'logo', 'logo_path'], ['agency_logo', 'agency-logo', 'agency_logo_path']] as [$field, $prefix, $column]) {
            $path = infoStoreUpload($field, $prefix);
            if ($path !== null) {
                $saved[]        = $path;
                $old[]          = $new[$column];
                $new[$column]   = $path;
            } elseif ((int) ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                infoFail($id, 'Fail tidak dapat disimpan di pelayan.', $typed);
            }
        }

        $userId = currentUserId();
        try {
            if ($current === null) {
                $newId = InfoModel::create($new, $userId);
                audit('CREATED', INFO_TABLE, $newId, null, $new);
                infoBack('success', 'Maklumat sistem berjaya disimpan.');
            }

            $before = InfoModel::auditSnapshot($current);
            [, $changed] = auditDiff($before, $new);
            if ($changed === []) {
                audit('NO_CHANGE', INFO_TABLE, $id, null, $new);
                infoBack('info', 'Tiada perubahan dibuat.');
            }

            InfoModel::update($id, $new, $userId);
            audit('UPDATED', INFO_TABLE, $id, $before, $new);
            array_map('infoDeleteUpload', $old);
            infoBack('success', 'Maklumat sistem berjaya dikemas kini.');
        } catch (PDOException $e) {
            array_map('infoDeleteUpload', $saved);
            error_log('[INFO] ' . $e->getMessage());
            infoFail($id, 'Ralat pangkalan data. Sila cuba lagi.', $typed);
        }
    }
}

if (!function_exists('infoPhp')) {
    /**
     * Information about PHP on this server (shown on the right side of the page).
     *
     * @return array{general:array<string,string>,config:array<string,string>,extensions:array<string,bool>}
     */
    function infoPhp(): array
    {
        $ini = static function (string $key): string {
            $value = ini_get($key);
            return $value === false || $value === '' ? '-' : (in_array(strtolower($value), ['0', 'off'], true) ? 'Off' : (in_array(strtolower($value), ['1', 'on'], true) ? 'On' : $value));
        };

        $extensions = [];
        foreach (['pdo_mysql', 'mbstring', 'curl', 'gd', 'zip', 'openssl', 'intl', 'xml', 'json', 'fileinfo', 'xdebug'] as $name) {
            $extensions[$name] = extension_loaded($name);
        }

        return [
            'general' => [
                'Versi PHP'          => PHP_VERSION,
                'Versi Zend Engine'  => zend_version(),
                'Server API'         => PHP_SAPI,
                'Sistem Pengendalian' => PHP_OS_FAMILY,
                'Zon Masa'           => date_default_timezone_get(),
            ],
            'config' => [
                'memory_limit'           => $ini('memory_limit'),
                'max_execution_time'     => $ini('max_execution_time'),
                'upload_max_filesize'    => $ini('upload_max_filesize'),
                'post_max_size'          => $ini('post_max_size'),
                'display_errors'         => $ini('display_errors'),
                'session.gc_maxlifetime' => $ini('session.gc_maxlifetime'),
            ],
            'extensions' => $extensions,
        ];
    }
}

if (!function_exists('handleInfo')) {
    /**
     * Run the System Info page logic.
     *
     * @return array{
     *   info: ?array<string,mixed>,
     *   old: ?array<string,mixed>,
     *   departments: array<int,array<string,mixed>>,
     *   php: array<string,mixed>,
     *   dbVersion: string,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleInfo(): array
    {
        // Only admins may see or change the system settings
        requireAuth();
        if (!isAdmin()) {
            audit('ACCESS_DENIED', INFO_TABLE, 0, null, ['role' => currentRole(), 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']);
            respondError(403, 'You do not have permission to access this page.');
        }

        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            infoSave();
        }

        // ---------- Normal page view ----------
        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['info_old'] ?? null;
        unset($_SESSION['info_old']);

        $dbVersion = InfoModel::databaseVersion();

        return [
            'info'        => InfoModel::get(),
            'old'         => is_array($old) ? $old : null,
            'departments' => InfoModel::departmentOptions(),
            'php'         => infoPhp(),
            'dbVersion'   => $dbVersion,
            'flash'       => pullFlash(),
        ];
    }
}
