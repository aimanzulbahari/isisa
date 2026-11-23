<?php
declare(strict_types=1);

/**
 * Helpdesk reference number.
 *
 * Format:  ISISA/HD/{year}/{user_id}/{running number, 5 digits}
 * Example: ISISA/HD/2026/1/00001
 *
 * - year           the current year (e.g. 2026)
 * - user_id        users.users_id of the person who creates the ticket
 * - running number 00001, 00002, ... The count starts again at 00001 every new year
 *                  and is shared by all users (so the number is never repeated in a year).
 *
 * The running number is kept in the table ref_no_counters (see database/migrations/main-db/ref_no_counters.sql).
 * It is taken with ONE atomic SQL statement, so two tickets created at the same moment never get the same number.
 *
 * Usage (inside the code that saves a new ticket):
 *   require_once APP_PATH . '/api/refNo/helpdeskRefNo.php';
 *   $refNo = generateHelpdeskRefNo(currentUserId());
 *
 * Tip: call it in the SAME transaction that inserts the ticket. If the insert fails and the transaction
 * is rolled back, the number is given back and no gap is left in the list.
 */

const HELPDESK_REFNO_PREFIX = 'ISISA/HD';
const HELPDESK_REFNO_DIGITS = 5;

if (!function_exists('formatHelpdeskRefNo')) {
    /** Build the text only (no database). formatHelpdeskRefNo(2026, 1, 7) -> "ISISA/HD/2026/1/00007" */
    function formatHelpdeskRefNo(int $year, int $userId, int $number): string
    {
        return sprintf(
            '%s/%d/%d/%0' . HELPDESK_REFNO_DIGITS . 'd',
            HELPDESK_REFNO_PREFIX,
            $year,
            $userId,
            $number
        );
    }
}

if (!function_exists('generateHelpdeskRefNo')) {
    /**
     * Give out the next helpdesk reference number for a user.
     *
     * @param int|null $year Only for tests. Default: the current year.
     * @throws InvalidArgumentException when the user id is not valid.
     */
    function generateHelpdeskRefNo(int $userId, ?int $year = null): string
    {
        if ($userId < 1) {
            throw new InvalidArgumentException('A valid user id is required to make a helpdesk reference number.');
        }
        $year ??= (int) date('Y');

        $pdo = db();

        // Create the row for this year on first use, otherwise add 1.
        // LAST_INSERT_ID(expr) keeps the new value for THIS connection, so we can read it back safely.
        $pdo->prepare(
            'INSERT INTO ref_no_counters (prefix, year, last_no)
             VALUES (:prefix, :year, LAST_INSERT_ID(1))
             ON DUPLICATE KEY UPDATE last_no = LAST_INSERT_ID(last_no + 1)'
        )->execute(['prefix' => HELPDESK_REFNO_PREFIX, 'year' => $year]);

        $number = (int) $pdo->lastInsertId();

        return formatHelpdeskRefNo($year, $userId, $number);
    }
}
