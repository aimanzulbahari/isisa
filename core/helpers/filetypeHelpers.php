<?php
declare(strict_types=1);

/**
 * File type and upload helpers.
 * Allowed file types are stored in the ref_file_type table (PDF, DOCX, XLSX, JPG, PNG...).
 */

if (!function_exists('mimeMap')) {
    /**
     * Allowed extensions and the real MIME types each one may have.
     * The MIME type is read from the file content, so a renamed file is caught.
     */
    function mimeMap(): array
    {
        return [
            'pdf'  => ['application/pdf'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
        ];
    }
}

if (!function_exists('getFileExtension')) {
    /** Lowercase extension without the dot: "Report.PDF" -> "pdf" */
    function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}

if (!function_exists('getAllowedExtensions')) {
    /**
     * Extensions allowed by the system (active rows of ref_file_type).
     *
     * @return string[] e.g. ['pdf', 'docx', 'xlsx', 'jpg', 'jpeg', 'png']
     */
    function getAllowedExtensions(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $stmt = db()->prepare('SELECT ref_file_type_code FROM ref_file_type WHERE ref_status_id = :active');
        $stmt->execute(['active' => config('app.status')['ACTIVE']]);

        $cache = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $code) {
            $ext     = strtolower((string) $code);
            $cache[] = $ext;
            if ($ext === 'jpg') {
                $cache[] = 'jpeg'; // Same type, two spellings
            }
        }
        return $cache;
    }
}

if (!function_exists('safeFileName')) {
    /**
     * A safe, unique file name for saving on the server.
     * The original name is not used for storage (it may contain tricks like "../").
     */
    function safeFileName(string $original): string
    {
        $base = slugify(pathinfo($original, PATHINFO_FILENAME)) ?: 'file';
        return date('Ymd_His') . '_' . randomString(8) . '_' . substr($base, 0, 40) . '.' . getFileExtension($original);
    }
}

if (!function_exists('validateUpload')) {
    /**
     * Check one uploaded file ($_FILES['x']).
     *
     * @param string[]|null $allowed Allowed extensions. null = use ref_file_type from the database.
     * @return string|null An error message, or null when the file is fine.
     */
    function validateUpload(array $file, ?array $allowed = null, ?int $maxBytes = null): ?string
    {
        $allowed  ??= getAllowedExtensions();
        $maxBytes ??= (int) config('app.upload.max_size');

        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return 'The file is too large.';
        }
        if ($error !== UPLOAD_ERR_OK) {
            return 'The file could not be uploaded. Please try again.';
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            return 'Invalid upload.';
        }
        if ($file['size'] > $maxBytes) {
            return 'The file is too large (maximum ' . formatBytes($maxBytes) . ').';
        }

        $ext = getFileExtension((string) $file['name']);
        if (!in_array($ext, $allowed, true) || !isset(mimeMap()[$ext])) {
            return 'File type not allowed. Allowed: ' . strtoupper(implode(', ', $allowed)) . '.';
        }

        // Check the real content type, not only the name
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, mimeMap()[$ext], true)) {
            return 'The file content does not match its type.';
        }
        return null;
    }
}

if (!function_exists('saveUpload')) {
    /**
     * Move a checked upload into storage/uploads/{$folder}.
     *
     * @return string|null The saved file name, or null if it failed.
     */
    function saveUpload(array $file, string $folder = ''): ?string
    {
        $dir = rtrim(UPLOAD_PATH . '/' . trim($folder, '/'), '/');
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            return null;
        }

        $name = safeFileName((string) $file['name']);
        return move_uploaded_file($file['tmp_name'], $dir . '/' . $name) ? $name : null;
    }
}
