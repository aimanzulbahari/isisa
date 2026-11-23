<?php
declare(strict_types=1);

/**
 * LoginModel: database queries for the login page.
 *
 * Only talks to the database. No sessions, redirects or HTML here.
 * Related tables: users, ref_status, login_logs.
 * (Login logs and lockout checks are in core/helpers/authHelpers.php.)
 */
final class LoginModel
{
    /**
     * Find a user by IC number (myID), including the password hash for verification.
     * The hash must never leave the controller.
     *
     * @return array{users_id:int,myID:string,fullname:string,password:string,ref_status_id:int,ref_role_code:string}|null
     */
    public static function findByMyID(string $myID): ?array
    {
        $stmt = db()->prepare(
            'SELECT u.users_id, u.myID, u.fullname, u.password, u.ref_status_id, r.ref_role_code
             FROM users u
             JOIN ref_role r ON r.ref_role_id = u.ref_role_id
             WHERE u.myID = :myID
             LIMIT 1'
        );
        $stmt->execute(['myID' => $myID]);

        return $stmt->fetch() ?: null;
    }

    /** Save a new password hash (used when the old hash needs a stronger algorithm). */
    public static function updatePasswordHash(int $userId, string $hash): void
    {
        db()->prepare('UPDATE users SET password = :hash WHERE users_id = :id')
            ->execute(['hash' => $hash, 'id' => $userId]);
    }
}
