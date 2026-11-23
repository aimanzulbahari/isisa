<?php
declare(strict_types=1);

/**
 * ListController: the logic of the Helpdesk list page (List.php).
 *
 * List.php calls handleHelpdeskList() once. The function:
 *   - on a normal page view (GET): returns the tickets, statistics, pagination, the choices
 *     for the form and the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST, action=create): validates, saves the attachment, creates the
 *     reference number, saves the ticket, writes the audit log, then ALWAYS redirects
 *     (Post/Redirect/Get), so pressing Refresh never sends the ticket twice.
 *
 * helpdeskDownload() sends an attachment to the browser (route "meja-attachment").
 *
 * Who may see a ticket: a normal user sees only his / her own tickets,
 * Admin and Superadmin see all of them (see helpdeskScopeUserId()).
 *
 * Attachments are saved OUTSIDE the web folder, in  storage/uploads/helpdesk/{user_id}/
 * (the folder of the user who created the ticket). The browser can never open that
 * folder directly (.htaccess blocks /storage), only helpdeskDownload() can serve a file,
 * after it checks the login and who owns the ticket.
 *
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits, auditable_type = helpdesk):
 *   CREATED        a ticket was created
 *   CREATE_FAILED  creating was refused (validation / file problem / database error)
 *   INVALID_ACTION a POST with an unknown action
 * audits.auditable_id is required, so a failed CREATE uses 0 (no ticket exists yet).
 */

require_once __DIR__ . '/ListModel.php';
require_once APP_PATH . '/api/refNo/helpdeskRefNo.php';

const HELPDESK_TABLE       = 'helpdesk';
const HELPDESK_PER_PAGE    = 10;
const HELPDESK_TITLE_MAX   = 255;
const HELPDESK_DESC_MAX    = 1000;
const HELPDESK_FILE_MAX    = 5 * 1024 * 1024;   // 5 MB
const HELPDESK_UPLOAD_DIR  = 'helpdesk';        // inside storage/uploads
/** Allowed attachments: extension => allowed MIME types */
const HELPDESK_FILE_TYPES  = [
    'pdf'  => ['application/pdf'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png'],
];

if (!function_exists('helpdeskScopeUserId')) {
    /** null = may see all tickets (Admin / Superadmin). Otherwise only the tickets of this user. */
    function helpdeskScopeUserId(): ?int
    {
        return isAdmin() ? null : currentUserId();
    }
}

if (!function_exists('helpdeskListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function helpdeskListQuery(array $source): array
    {
        $query = [];

        $q = trim((string) ($source['q'] ?? ''));
        if ($q !== '') {
            $query['q'] = mb_substr($q, 0, 100);
        }
        $status = toInt($source['status'] ?? 0);
        if ($status > 0) {
            $query['status'] = $status;
        }
        $page = toInt($source['page'] ?? 1, 1);
        if ($page > 1) {
            $query['page'] = $page;
        }
        return $query;
    }
}

if (!function_exists('helpdeskBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the list.
     *
     * @param array<string,mixed>|null $old Typed values, to re-open the form with them
     */
    function helpdeskBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['helpdesk_old'] = $old;
        }
        redirect(routeUrl('meja-list', helpdeskListQuery($_POST)), 303);
    }
}

if (!function_exists('helpdeskFail')) {
    /**
     * Creating was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values
     */
    function helpdeskFail(string $message, array $old): never
    {
        audit('CREATE_FAILED', HELPDESK_TABLE, 0, null, ['reason' => $message] + $old);
        helpdeskBack('error', $message, $old);
    }
}

if (!function_exists('helpdeskText')) {
    /** A text input: trimmed, spaces collapsed (line breaks kept for multi-line fields). */
    function helpdeskText(string $key, bool $multiline = false): string
    {
        $value = trim((string) ($_POST[$key] ?? ''));

        return $multiline
            ? trim((string) preg_replace('/[ \t]+/u', ' ', str_replace("\r\n", "\n", $value)))
            : trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}

if (!function_exists('helpdeskCheckUpload')) {
    /**
     * Check the attachment BEFORE anything is saved.
     * The type is read from the file content, so a renamed file is caught.
     *
     * @return string|null An error message, or null when the file is fine (or no file was chosen)
     */
    function helpdeskCheckUpload(string $field = 'attachment'): ?string
    {
        $file = $_FILES[$field] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;   // The attachment is optional
        }

        $error = (int) $file['error'];
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return 'Fail lampiran terlalu besar (maksimum 5 MB).';
        }
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) $file['tmp_name'])) {
            return 'Fail lampiran gagal dimuat naik. Sila cuba lagi.';
        }
        if ((int) $file['size'] > HELPDESK_FILE_MAX) {
            return 'Fail lampiran terlalu besar (maksimum 5 MB).';
        }

        $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!isset(HELPDESK_FILE_TYPES[$ext])) {
            return 'Fail lampiran mesti dalam format PDF, JPG atau PNG.';
        }

        $mime = (string) (new finfo(FILEINFO_MIME_TYPE))->file((string) $file['tmp_name']);
        if (!in_array($mime, HELPDESK_FILE_TYPES[$ext], true)) {
            return 'Fail lampiran bukan fail PDF, JPG atau PNG yang sah.';
        }
        if ($ext !== 'pdf' && @getimagesize((string) $file['tmp_name']) === false) {
            return 'Fail lampiran bukan imej yang sah.';
        }
        return null;
    }
}

if (!function_exists('helpdeskStoreUpload')) {
    /**
     * Save a file that passed helpdeskCheckUpload() in storage/uploads/helpdesk/{user_id}/.
     *
     * @return string|null The path to keep in the database, relative to storage/uploads
     *                     (e.g. "helpdesk/1/20261120-ab12cd34ef567890.pdf"), or null when no file was chosen
     * @throws RuntimeException when the file cannot be saved
     */
    function helpdeskStoreUpload(int $userId, string $field = 'attachment'): ?string
    {
        $file = $_FILES[$field] ?? null;
        if (!is_array($file) || (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        $relativeDir = HELPDESK_UPLOAD_DIR . '/' . $userId;
        $dir         = UPLOAD_PATH . '/' . $relativeDir;
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Cannot create the upload folder.');
        }

        // Random name: the original name is never used
        $ext  = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $ext;

        if (!move_uploaded_file((string) $file['tmp_name'], $dir . '/' . $name)) {
            throw new RuntimeException('Cannot save the uploaded file.');
        }
        return $relativeDir . '/' . $name;
    }
}

if (!function_exists('helpdeskAttachmentFile')) {
    /**
     * The real file on disk for a path kept in the database.
     * Returns null unless the file really is inside storage/uploads/helpdesk (blocks "../" tricks).
     */
    function helpdeskAttachmentFile(?string $relative): ?string
    {
        if ($relative === null || $relative === '' || !str_starts_with($relative, HELPDESK_UPLOAD_DIR . '/')) {
            return null;
        }
        $base = realpath(UPLOAD_PATH . '/' . HELPDESK_UPLOAD_DIR);
        $file = realpath(UPLOAD_PATH . '/' . $relative);

        if ($base === false || $file === false || !is_file($file) || !str_starts_with($file, $base . DIRECTORY_SEPARATOR)) {
            return null;
        }
        return $file;
    }
}

if (!function_exists('helpdeskDeleteUpload')) {
    /** Remove a saved attachment (used when the ticket could not be saved). */
    function helpdeskDeleteUpload(?string $relative): void
    {
        $file = helpdeskAttachmentFile($relative);
        if ($file !== null) {
            @unlink($file);
        }
    }
}

if (!function_exists('helpdeskCreate')) {
    /** Handle POST action=create. */
    function helpdeskCreate(): never
    {
        $userId = (int) currentUserId();

        // 1. Read the typed values
        $old = [
            'type_id'     => toInt($_POST['type_id'] ?? 0),
            'severity_id' => toInt($_POST['severity_id'] ?? 0),
            'title'       => helpdeskText('title'),
            'description' => helpdeskText('description', true),
        ];

        // 2. Input check
        if ($old['type_id'] <= 0 || !HelpdeskListModel::typeExists($old['type_id'])) {
            helpdeskFail('Sila pilih jenis aduan yang sah.', $old);
        }
        if ($old['severity_id'] <= 0 || !HelpdeskListModel::severityExists($old['severity_id'])) {
            helpdeskFail('Sila pilih tahap keterukan yang sah.', $old);
        }
        if ($old['title'] === '') {
            helpdeskFail('Sila isi Tajuk Aduan.', $old);
        }
        if (mb_strlen($old['title']) > HELPDESK_TITLE_MAX) {
            helpdeskFail('Tajuk Aduan terlalu panjang (maksimum ' . HELPDESK_TITLE_MAX . ' aksara).', $old);
        }
        if ($old['description'] === '') {
            helpdeskFail('Sila isi Keterangan Aduan.', $old);
        }
        if (mb_strlen($old['description']) > HELPDESK_DESC_MAX) {
            helpdeskFail('Keterangan Aduan terlalu panjang (maksimum ' . HELPDESK_DESC_MAX . ' aksara).', $old);
        }
        $fileError = helpdeskCheckUpload();
        if ($fileError !== null) {
            helpdeskFail($fileError, $old);
        }

        // 3. Save. The reference number, the file and the ticket are saved together:
        //    if anything fails, the number is given back and the file is removed.
        $pdo      = db();
        $savedPath = null;

        try {
            $pdo->beginTransaction();

            $refNo     = generateHelpdeskRefNo($userId);
            $savedPath = helpdeskStoreUpload($userId);

            $new = [
                'helpdesk_reference_no' => $refNo,
                'helpdesk_type_id'      => $old['type_id'],
                'helpdesk_severity_id'  => $old['severity_id'],
                'helpdesk_title'        => $old['title'],
                'helpdesk_description'  => $old['description'],
                'helpdesk_attachment'   => $savedPath,
                'status_id'             => (int) config('app.status')['TICKET_OPEN'],
            ];
            $id = HelpdeskListModel::create($new, $userId);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            helpdeskDeleteUpload($savedPath);
            error_log('[HELPDESK] ' . $e->getMessage());
            helpdeskFail('Aduan tidak dapat disimpan. Sila cuba lagi.', $old);
        }

        audit('CREATED', HELPDESK_TABLE, $id, null, $new);
        helpdeskBack('success', "Aduan berjaya dihantar. No. Rujukan: {$refNo}");
    }
}

if (!function_exists('helpdeskDownload')) {
    /**
     * Send an attachment to the browser: /Helpdesk/Attachment?id=<ticket id>
     * Only the owner of the ticket, Admin and Superadmin may download it.
     */
    function helpdeskDownload(): never
    {
        requireAuth();

        $ticket = HelpdeskListModel::findById(toInt($_GET['id'] ?? 0), helpdeskScopeUserId());
        $file   = $ticket !== null ? helpdeskAttachmentFile($ticket['helpdesk_attachment']) : null;

        if ($file === null) {
            respondError(404, 'Lampiran tidak ditemui.');
        }

        $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $mime = HELPDESK_FILE_TYPES[$ext][0] ?? 'application/octet-stream';
        $name = preg_replace('/[^A-Za-z0-9._-]+/', '-', (string) $ticket['helpdesk_reference_no']) . '.' . $ext;

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: inline; filename="' . $name . '"');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, max-age=0, must-revalidate');
        readfile($file);
        exit;
    }
}

if (!function_exists('handleHelpdeskList')) {
    /**
     * Run the Helpdesk list page logic.
     *
     * @return array{
     *   rows: array<int,array<string,mixed>>,
     *   stats: array{total:int,open:int,in_progress:int,closed:int},
     *   page: array<string,mixed>,
     *   statusOptions: array<int,array<string,mixed>>,
     *   typeOptions: array<int,array<string,mixed>>,
     *   severityOptions: array<int,array<string,mixed>>,
     *   showCreator: bool,
     *   q: string,
     *   status: int,
     *   old: ?array<string,mixed>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleHelpdeskList(): array
    {
        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $action = (string) ($_POST['action'] ?? '');

            if ($action !== 'create') {
                audit('INVALID_ACTION', HELPDESK_TABLE, 0, null, ['action' => mb_substr($action, 0, 50)]);
                helpdeskBack('error', 'Tindakan tidak sah.');
            }
            helpdeskCreate();
        }

        // ---------- Normal page view ----------
        $scope  = helpdeskScopeUserId();
        $query  = helpdeskListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        // Only the ticket statuses are valid filters
        if (!in_array($status, HelpdeskListModel::ticketStatusIds(), true)) {
            $status = 0;
        }

        $page = paginate(HelpdeskListModel::count($q, $status, $scope), currentPage(), HELPDESK_PER_PAGE);

        // What the user typed when the last save was refused (shown once)
        $old = $_SESSION['helpdesk_old'] ?? null;
        unset($_SESSION['helpdesk_old']);

        return [
            'rows'            => HelpdeskListModel::search($q, $status, $scope, $page['per_page'], $page['offset']),
            'stats'           => HelpdeskListModel::stats($scope),
            'page'            => $page,
            'statusOptions'   => HelpdeskListModel::statusOptions(),
            'typeOptions'     => HelpdeskListModel::typeOptions(),
            'severityOptions' => HelpdeskListModel::severityOptions(),
            'showCreator'     => $scope === null,   // Admin sees who created each ticket
            'q'               => $q,
            'status'          => $status,
            'old'             => is_array($old) ? $old : null,
            'flash'           => pullFlash(),
        ];
    }
}
