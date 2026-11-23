<?php
declare(strict_types=1);

/**
 * ScreenLockModel: database queries for the Lock-Screen page.
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related table: users.
 */
final class ScreenLockModel
{
    /**
     * The password hash of one user, used to check the password typed on the lock screen.
     * The hash must never leave the controller.
     */
    public static function passwordHash(int $userId): ?string
    {
        $stmt = db()->prepare('SELECT password FROM users WHERE users_id = :id LIMIT 1');
        $stmt->execute(['id' => $userId]);

        $hash = $stmt->fetchColumn();
        return $hash === false ? null : (string) $hash;
    }
}
