<?php
declare(strict_types=1);

/**
 * MyKad / IC number (users.myID) helpers.
 *
 * A Malaysian IC number has 12 digits:  YYMMDD-PB-###G
 *   YYMMDD = date of birth, PB = place of birth, G = last digit (odd = male, even = female)
 */

if (!function_exists('cleanMyID')) {
    /** Remove dashes and spaces: "900101-10-5544" -> "900101105544" */
    function cleanMyID(string $myID): string
    {
        return preg_replace('/\D/', '', $myID) ?? '';
    }
}

if (!function_exists('extractDobFromMyID')) {
    /**
     * Get the date of birth ("Y-m-d") from an IC number, or null if invalid.
     *
     * The IC only has 2 digits for the year, so we guess the century:
     * a year bigger than this year's (e.g. 95 in 2026) must be 1900s, otherwise 2000s.
     */
    function extractDobFromMyID(string $myID): ?string
    {
        $id = cleanMyID($myID);
        if (strlen($id) !== 12) {
            return null;
        }

        $yy = (int) substr($id, 0, 2);
        $mm = (int) substr($id, 2, 2);
        $dd = (int) substr($id, 4, 2);

        $year = $yy > (int) date('y') ? 1900 + $yy : 2000 + $yy;

        return checkdate($mm, $dd, $year) ? sprintf('%04d-%02d-%02d', $year, $mm, $dd) : null;
    }
}

if (!function_exists('isValidMyID')) {
    /** 12 digits and a real date of birth. */
    function isValidMyID(string $myID): bool
    {
        return extractDobFromMyID($myID) !== null;
    }
}

if (!function_exists('formatMyID')) {
    /** "900101105544" -> "900101-10-5544" */
    function formatMyID(string $myID): string
    {
        $id = cleanMyID($myID);
        return strlen($id) === 12 ? substr($id, 0, 6) . '-' . substr($id, 6, 2) . '-' . substr($id, 8) : $myID;
    }
}

if (!function_exists('getGenderFromMyID')) {
    /** Returns "M" (male), "F" (female) or null when the IC is not valid. */
    function getGenderFromMyID(string $myID): ?string
    {
        $id = cleanMyID($myID);
        if (!isValidMyID($id)) {
            return null;
        }
        return ((int) substr($id, -1)) % 2 === 1 ? 'M' : 'F';
    }
}
