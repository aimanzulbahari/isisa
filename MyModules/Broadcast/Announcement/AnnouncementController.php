<?php
declare(strict_types=1);

/**
 * AnnouncementController: the logic of the Announcement page (Announcement.php).
 *
 * Announcement.php calls handleAnnouncement() once. The function:
 *   - only lets ADMIN / SUPERADMIN in (anyone else gets 403 and the attempt is audited)
 *   - on a normal page view (GET): returns one page of the list, the numbers for the cards, the
 *     dropdown options and the messages to show. Search / filter / page come from the URL
 *     (?q=...&status=...&page=...), so everything is done on the server.
 *   - on a form submit (POST): validates, saves (new, or changed when announcement_id is sent),
 *     writes the audit log, then ALWAYS redirects (Post/Redirect/Get)
 *
 * There is no delete. To hide an announcement, set its status to "Tidak Aktif".
 * CSRF is already checked for every POST by core/includes/csrf.php.
 *
 * Audit log (table: audits, auditable_type = announcement):
 *   CREATED        a new announcement was saved
 *   UPDATED        an announcement was changed (only the changed fields are saved)
 *   NO_CHANGE      the form was saved without any change
 *   UPDATE_FAILED  saving was refused (validation / database error)
 *   ACCESS_DENIED  a user without the right role tried to open the page
 * audits.auditable_id is required, so a refused NEW announcement uses 0 (no row exists yet).
 */

require_once __DIR__ . '/AnnouncementModel.php';

const ANNOUNCEMENT_TABLE = 'announcement';
const ANNOUNCEMENT_TITLE_MAX = 150;        // same as maxlength in the form
const ANNOUNCEMENT_DESCRIPTION_MAX = 500;  // same as maxlength in the form
const ANNOUNCEMENT_PER_PAGE = 10;

if (!function_exists('announcementListQuery')) {
    /**
     * The list filters (q, status, page) that are kept in the URL.
     *
     * @param  array<string,mixed> $source $_GET or $_POST
     * @return array<string,string|int>
     */
    function announcementListQuery(array $source): array
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

if (!function_exists('announcementBack')) {
    /**
     * Save a message (and, on error, what the user typed) then go back to the page.
     *
     * @param array<string,mixed>|null $old Typed values (form field names), to fill the form again
     * Search / filter / page are kept (they are sent by the form as hidden fields).
     */
    function announcementBack(string $type, string $message, ?array $old = null): never
    {
        setFlash($type, $message);

        if ($old !== null) {
            $_SESSION['announcement_old'] = $old;
        }
        redirect(routeUrl('announcement', announcementListQuery($_POST)), 303);
    }
}

if (!function_exists('announcementFail')) {
    /**
     * A save was refused: write it to the audit log, then go back with the message.
     *
     * @param array<string,mixed> $old Typed values (form field names)
     */
    function announcementFail(int $id, string $message, array $old): never
    {
        audit('UPDATE_FAILED', ANNOUNCEMENT_TABLE, $id, null, ['reason' => $message] + $old);
        announcementBack('error', $message, $old);
    }
}

if (!function_exists('announcementText')) {
    /** A text input: trimmed, spaces collapsed (line breaks kept for multi-line fields). */
    function announcementText(string $key, bool $multiline = false): string
    {
        $value = trim((string) ($_POST[$key] ?? ''));

        return $multiline
            ? trim((string) preg_replace('/[ \t]+/u', ' ', str_replace("\r\n", "\n", $value)))
            : trim((string) preg_replace('/\s+/u', ' ', $value));
    }
}

if (!function_exists('announcementSave')) {
    /** Handle the form POST: a new announcement, or a change when announcement_id is sent. */
    function announcementSave(): never
    {
        // 1. Which announcement? 0 = a new one
        $id      = toInt($_POST['announcement_id'] ?? 0);
        $current = null;
        if ($id > 0) {
            $current = AnnouncementModel::find($id);
            if ($current === null) {
                announcementFail($id, 'Pengumuman tidak dijumpai.', []);
            }
        }
        $auditId = $current !== null ? $id : 0;

        // 2. Read the typed values (keys = form field names, so the form can be filled again)
        $typed = [
            'announcement_id' => $id > 0 ? (string) $id : '',
            'tajuk'           => announcementText('tajuk'),
            'jenis'           => announcementText('jenis'),
            'keterangan'      => announcementText('keterangan', true),
            'tarikh_mula'     => announcementText('tarikh_mula'),
            'tarikh_tamat'    => announcementText('tarikh_tamat'),
            'status_id'       => announcementText('status_id'),
        ];

        // 3. Input check
        if ($typed['tajuk'] === '') {
            announcementFail($auditId, 'Sila isi Tajuk Pengumuman.', $typed);
        }
        if (mb_strlen($typed['tajuk']) > ANNOUNCEMENT_TITLE_MAX) {
            announcementFail($auditId, 'Tajuk Pengumuman terlalu panjang (maksimum ' . ANNOUNCEMENT_TITLE_MAX . ' aksara).', $typed);
        }

        $typeId = toInt($typed['jenis']);
        if ($typeId <= 0 || !AnnouncementModel::typeExists($typeId)) {
            announcementFail($auditId, 'Sila pilih Jenis Pengumuman yang sah.', $typed);
        }

        if ($typed['keterangan'] === '') {
            announcementFail($auditId, 'Sila isi Keterangan Pengumuman.', $typed);
        }
        if (mb_strlen($typed['keterangan']) > ANNOUNCEMENT_DESCRIPTION_MAX) {
            announcementFail($auditId, 'Keterangan terlalu panjang (maksimum ' . ANNOUNCEMENT_DESCRIPTION_MAX . ' aksara).', $typed);
        }

        $start = toDbDate($typed['tarikh_mula']);
        if ($start === null) {
            announcementFail($auditId, 'Sila pilih Tarikh Mula yang sah.', $typed);
        }
        $end = null; // an empty end date means "no end date"
        if ($typed['tarikh_tamat'] !== '') {
            $end = toDbDate($typed['tarikh_tamat']);
            if ($end === null) {
                announcementFail($auditId, 'Tarikh Tamat tidak sah.', $typed);
            }
            if ($end < $start) {
                announcementFail($auditId, 'Tarikh Tamat tidak boleh sebelum Tarikh Mula.', $typed);
            }
        }

        $statusId = toInt($typed['status_id']);
        $allowed  = array_map('intval', array_column(AnnouncementModel::statusOptions(), 'ref_status_id'));
        if (!in_array($statusId, $allowed, true)) {
            announcementFail($auditId, 'Sila pilih Status yang sah.', $typed);
        }

        // 4. Save. A date becomes a whole day: the start at 00:00:00 and the end at 23:59:59.
        $new = [
            'announcement_title'       => $typed['tajuk'],
            'announcement_type_id'     => $typeId,
            'announcement_description' => $typed['keterangan'],
            'announcement_start_date'  => $start . ' 00:00:00',
            'announcement_end_date'    => $end !== null ? $end . ' 23:59:59' : null,
            'announcement_status_id'   => $statusId,
        ];

        $userId = currentUserId();
        try {
            if ($current === null) {
                $newId = AnnouncementModel::create($new, $userId);
                audit('CREATED', ANNOUNCEMENT_TABLE, $newId, null, $new);
                announcementBack('success', 'Pengumuman berjaya ditambah.');
            }

            $before = AnnouncementModel::auditSnapshot($current);
            [, $changed] = auditDiff($before, $new);
            if ($changed === []) {
                audit('NO_CHANGE', ANNOUNCEMENT_TABLE, $id, null, $new);
                announcementBack('info', 'Tiada perubahan dibuat.');
            }

            AnnouncementModel::update($id, $new, $userId);
            audit('UPDATED', ANNOUNCEMENT_TABLE, $id, $before, $new);
            announcementBack('success', 'Pengumuman berjaya dikemas kini.');
        } catch (PDOException $e) {
            error_log('[ANNOUNCEMENT] ' . $e->getMessage());
            announcementFail($auditId, 'Ralat pangkalan data. Sila cuba lagi.', $typed);
        }
    }
}

if (!function_exists('handleAnnouncement')) {
    /**
     * Run the Announcement page logic.
     *
     * @return array{
     *   items: array<int,array<string,mixed>>,
     *   page: array<string,mixed>,
     *   q: string,
     *   status: int,
     *   stats: array{total:int,active:int,not_active:int,last_update:?string},
     *   types: array<int,array<string,mixed>>,
     *   statuses: array<int,array<string,mixed>>,
     *   old: ?array<string,string>,
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleAnnouncement(): array
    {
        // Only admins may see or change announcements
        requireAuth();
        if (!isAdmin()) {
            audit('ACCESS_DENIED', ANNOUNCEMENT_TABLE, 0, null, ['role' => currentRole(), 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']);
            respondError(403, 'You do not have permission to access this page.');
        }

        // ---------- Form submitted ----------
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            announcementSave();
        }

        // ---------- Normal page view ----------
        $query  = announcementListQuery($_GET);
        $q      = (string) ($query['q'] ?? '');
        $status = (int) ($query['status'] ?? 0);

        $page = paginate(AnnouncementModel::count($q, $status), currentPage(), ANNOUNCEMENT_PER_PAGE);

        // What the user typed when the last save was refused (shown once, the form opens again with it)
        $old = $_SESSION['announcement_old'] ?? null;
        unset($_SESSION['announcement_old']);

        return [
            'items'    => AnnouncementModel::search($q, $status, $page['per_page'], $page['offset']),
            'page'     => $page,
            'stats'    => AnnouncementModel::stats(),
            'types'    => AnnouncementModel::typeOptions(),
            'statuses' => AnnouncementModel::statusOptions(),
            'q'        => $q,
            'status'   => $status,
            'old'      => is_array($old) ? $old : null,
            'flash'    => pullFlash(),
        ];
    }
}
