<?php
declare(strict_types=1);

/**
 * LogoutModel: database queries for logging out.
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related table: login_logs.
 * (The session row itself is removed by the session handler, see core/includes/session-handlers.php.)
 */
final class LogoutModel
{
    /**
     * Fill in the logout time of the login that belongs to this session.
     * Only rows that are still "open" (logout_at is empty) are changed, so calling it twice is safe.
     *
     * @return int Number of rows updated (0 when the login was not found or was already closed).
     */
    public static function closeLoginLog(string $sessionId): int
    {
        if ($sessionId === '') {
            return 0;
        }

        $stmt = db()->prepare(
            'UPDATE login_logs
             SET logout_at = NOW()
             WHERE session_id = :sid AND logout_at IS NULL'
        );
        $stmt->execute(['sid' => $sessionId]);

        return $stmt->rowCount();
    }
}
