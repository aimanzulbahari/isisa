<?php
declare(strict_types=1);

/**
 * Age helpers (used for users.age).
 */

if (!function_exists('calculateAge')) {
    /**
     * Age in full years from a date of birth ("Y-m-d").
     * Returns null when the date is empty, invalid or in the future.
     *
     * @param string|null $onDate Count the age on this date instead of today.
     */
    function calculateAge(?string $dob, ?string $onDate = null): ?int
    {
        if (isBlank($dob)) {
            return null;
        }

        try {
            $birth = new DateTimeImmutable($dob);
            $today = new DateTimeImmutable($onDate ?? 'today');
        } catch (Exception) {
            return null;
        }

        return $birth > $today ? null : $birth->diff($today)->y;
    }
}

if (!function_exists('getAgeFromMyID')) {
    /** Age worked out straight from an IC number. */
    function getAgeFromMyID(string $myID): ?int
    {
        return calculateAge(extractDobFromMyID($myID));
    }
}
