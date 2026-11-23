<?php
declare(strict_types=1);

/**
 * Display formatting helpers (phone numbers, status badges, yes/no...).
 */

if (!function_exists('formatPhone')) {
    /**
     * Format a Malaysian phone number for display.
     *   "0123456789"   -> "012-345 6789"
     *   "+60123456789" -> "012-345 6789"
     * Anything that is not recognised is returned as it was typed.
     */
    function formatPhone(?string $phone): string
    {
        $original = (string) $phone;
        $digits   = preg_replace('/\D/', '', $original) ?? '';

        if (str_starts_with($digits, '60')) {
            $digits = '0' . substr($digits, 2);
        }
        if (!preg_match('/^01\d{8,9}$/', $digits)) {
            return $original === '' ? '-' : $original;
        }
        return substr($digits, 0, 3) . '-' . substr($digits, 3, 3) . ' ' . substr($digits, 6);
    }
}

if (!function_exists('formatYesNo')) {
    function formatYesNo(mixed $value): string
    {
        return $value ? __('yes', [], 'Yes') : __('no', [], 'No');
    }
}

if (!function_exists('statusBadge')) {
    /**
     * Coloured Bootstrap badge for a ref_status ID.
     * Status IDs come from config('app.status').
     */
    function statusBadge(int $statusId, ?string $label = null): string
    {
        $s = config('app.status');

        [$class, $text] = match ($statusId) {
            $s['ACTIVE'], $s['SUCCESS'] => ['success', 'Active'],
            $s['NOT_ACTIVE']            => ['secondary', 'Not Active'],
            $s['FAILED']                => ['danger', 'Failed'],
            $s['LOCKED']                => ['warning text-dark', 'Locked'],
            // Helpdesk ticket statuses
            $s['TICKET_OPEN']           => ['info text-dark', 'Open'],
            $s['TICKET_IN_PROGRESS']    => ['primary', 'In Progress'],
            $s['TICKET_ON_HOLD']        => ['warning text-dark', 'On Hold'],
            $s['TICKET_TRANSFERRED']    => ['secondary', 'Transferred'],
            $s['TICKET_CLOSED']         => ['success', 'Closed'],
            default                     => ['light text-dark', 'Unknown'],
        };

        // A custom label (e.g. from ref_status_description) wins over the default text
        return '<span class="badge bg-' . $class . '">' . e($label ?? $text) . '</span>';
    }
}

if (!function_exists('nl2brSafe')) {
    /** Keep line breaks of user text, but escape the HTML first. */
    function nl2brSafe(?string $text): string
    {
        return nl2br(e($text));
    }
}

if (!function_exists('displayValue')) {
    /** Show "-" for empty values in tables. */
    function displayValue(mixed $value): string
    {
        return isBlank($value) ? '-' : e($value);
    }
}
