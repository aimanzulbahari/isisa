<?php
declare(strict_types=1);

/**
 * AuditController: the logic of the Audit Trail page (Audit.php).
 *
 * Audit.php calls handleAudit() once. The function:
 *   - only lets ADMIN / SUPERADMIN in (anyone else gets 403 and the attempt is audited)
 *   - returns one page of the audit log with the numbers for the cards. Search, filters
 *     and the page come from the URL (?q=&event=&type=&from=&to=&page=), so everything
 *     is done on the server.
 *
 * The page is read only: the audit log can never be changed or deleted from here.
 * Opening the page is NOT written to the log (it would add a row every time the log is
 * looked at). Things that go wrong ARE recorded:
 *   ACCESS_DENIED   a user without the right role tried to open the page
 *   INVALID_ACTION  something was posted to this read-only page
 * audits.auditable_id is required, so these use 0 (no record is involved).
 */

require_once __DIR__ . '/AuditModel.php';

const AUDIT_PAGE_TABLE = 'audits';
const AUDIT_PER_PAGE   = 10;

if (!function_exists('auditFilters')) {
    /**
     * The filters in the URL, cleaned. Empty filters are left out.
     *
     * @param  array<string,mixed> $source $_GET
     * @return array<string,string>
     */
    function auditFilters(array $source): array
    {
        $filters = [];

        $q = trim((string) ($source['q'] ?? ''));
        if ($q !== '') {
            $filters['q'] = mb_substr($q, 0, 100);
        }
        // event / table must be one of the values that really exist
        foreach (['event' => 'event', 'type' => 'auditable_type'] as $key => $column) {
            $value = trim((string) ($source[$key] ?? ''));
            if ($value !== '' && in_array($value, AuditModel::distinct($column), true)) {
                $filters[$key] = $value;
            }
        }
        // dates must be real dates (YYYY-MM-DD)
        foreach (['from', 'to'] as $key) {
            $value = trim((string) ($source[$key] ?? ''));
            $date  = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
            if ($date !== false && $date->format('Y-m-d') === $value) {
                $filters[$key] = $value;
            }
        }
        // "from" after "to": swap them instead of showing nothing
        if (isset($filters['from'], $filters['to']) && $filters['from'] > $filters['to']) {
            [$filters['from'], $filters['to']] = [$filters['to'], $filters['from']];
        }
        return $filters;
    }
}

if (!function_exists('auditEventClass')) {
    /** Colour of the badge for an event: success, warning, danger or neutral. */
    function auditEventClass(string $event): string
    {
        return match (true) {
            in_array($event, ['CREATED', 'LOGIN', 'RESTORED'], true)  => 'success',
            in_array($event, ['UPDATED', 'NO_CHANGE'], true)          => 'warning',
            $event === 'DELETED'
                || str_ends_with($event, '_FAILED')
                || in_array($event, ['LOCKED', 'LOGIN_LOCKED', 'ACCESS_DENIED', 'INVALID_ACTION'], true) => 'danger',
            default                                                   => 'neutral',
        };
    }
}

if (!function_exists('auditDecode')) {
    /**
     * old_values / new_values are JSON text. Turn them into an array (empty if there is none).
     *
     * @return array<string,mixed>
     */
    function auditDecode(?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }
        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }
}

if (!function_exists('auditDetail')) {
    /**
     * Everything the "Butiran" window shows for one row. Encoded as JSON into the button,
     * and written to the page with textContent in JavaScript (never as HTML).
     *
     * @param  array<string,mixed> $row
     * @return array<string,mixed>
     */
    function auditDetail(array $row): array
    {
        return [
            'id'      => (int) $row['audit_id'],
            'time'    => (string) $row['created_at'],
            'event'   => (string) $row['event'],
            'user'    => $row['fullname'] !== null ? $row['fullname'] . ' (' . $row['myID'] . ')' : 'Sistem / tiada pengguna',
            'type'    => (string) $row['auditable_type'],
            'record'  => (int) $row['auditable_id'],
            'url'     => (string) ($row['url'] ?? ''),
            'ip'      => (string) ($row['ip_address'] ?? ''),
            'agent'   => (string) ($row['user_agent'] ?? ''),
            'old'     => auditDecode($row['old_values'] ?? null),
            'new'     => auditDecode($row['new_values'] ?? null),
        ];
    }
}

if (!function_exists('handleAudit')) {
    /**
     * Run the Audit Trail page logic.
     *
     * @return array{
     *   rows: array<int,array<string,mixed>>,
     *   stats: array{total:int,today:int,users_today:int,last_activity:?string},
     *   page: array<string,mixed>,
     *   filters: array<string,string>,
     *   events: string[],
     *   types: string[],
     *   flash: array<int,array{type:string,message:string}>
     * }
     */
    function handleAudit(): array
    {
        // Only admins may read the audit log
        requireAuth();
        if (!isAdmin()) {
            audit('ACCESS_DENIED', AUDIT_PAGE_TABLE, 0, null, ['role' => currentRole(), 'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET']);
            respondError(403, 'You do not have permission to access this page.');
        }

        // This page has no form that saves anything
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            audit('INVALID_ACTION', AUDIT_PAGE_TABLE, 0, null, ['method' => 'POST']);
            setFlash('error', 'Tindakan tidak sah.');
            redirect(routeUrl('system-audit'), 303);
        }

        $filters = auditFilters($_GET);
        $page    = paginate(AuditModel::count($filters), currentPage(), AUDIT_PER_PAGE);

        $rows = AuditModel::search($filters, $page['per_page'], $page['offset']);
        foreach ($rows as &$row) {
            $row['detail'] = auditDetail($row);
        }
        unset($row);

        return [
            'rows'    => $rows,
            'stats'   => AuditModel::stats(),
            'page'    => $page,
            'filters' => $filters,
            'events'  => AuditModel::distinct('event'),
            'types'   => AuditModel::distinct('auditable_type'),
            'flash'   => pullFlash(),
        ];
    }
}
