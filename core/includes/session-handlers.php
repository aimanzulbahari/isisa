<?php
declare(strict_types=1);

/**
 * Database session handler.
 *
 * Stores PHP sessions in the `sessions` table
 * (see database/migrations/session.sql) instead of files.
 *
 * Why: sessions can be listed, expired and revoked per user, and each row
 * keeps the user ID, IP address and browser (user agent) for auditing.
 *
 * This file only registers the handler. session.php starts the session.
 */

final class DatabaseSessionHandler implements SessionHandlerInterface
{
    private int $lifetime;
    private string $table;

    public function __construct()
    {
        $this->lifetime = (int) config('app.session.lifetime');
        $this->table    = (string) config('app.session.table');
    }

    /** Called when the session starts. Nothing to prepare, the DB is ready. */
    public function open(string $path, string $name): bool
    {
        return true;
    }

    /** Called when the session closes. */
    public function close(): bool
    {
        return true;
    }

    /** Load the session data. Must return "" when the session is not found. */
    public function read(string $id): string|false
    {
        $stmt = db()->prepare(
            "SELECT payload FROM `{$this->table}`
             WHERE session_id = :id AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $payload = $stmt->fetchColumn();

        return $payload === false ? '' : (string) $payload;
    }

    /** Save the session data (insert a new row, or update the existing one). */
    public function write(string $id, string $data): bool
    {
        $stmt = db()->prepare(
            "INSERT INTO `{$this->table}`
                (session_id, user_id, ip_address, user_agent, payload, last_activity, expires_at)
             VALUES
                (:id, :user_id, :ip, :agent, :payload, :activity, FROM_UNIXTIME(:expires))
             ON DUPLICATE KEY UPDATE
                user_id       = VALUES(user_id),
                ip_address    = VALUES(ip_address),
                user_agent    = VALUES(user_agent),
                payload       = VALUES(payload),
                last_activity = VALUES(last_activity),
                expires_at    = VALUES(expires_at)"
        );

        return $stmt->execute([
            'id'       => $id,
            // $_SESSION['user_id'] is set by the login process (null for guests)
            'user_id'  => $_SESSION['user_id'] ?? null,
            'ip'       => substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            'agent'    => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'payload'  => $data,
            'activity' => time(),
            'expires'  => time() + $this->lifetime,
        ]);
    }

    /** Delete one session (used by logout and session_regenerate_id). */
    public function destroy(string $id): bool
    {
        $stmt = db()->prepare("DELETE FROM `{$this->table}` WHERE session_id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /** Delete all expired sessions. PHP calls this now and then automatically. */
    public function gc(int $max_lifetime): int|false
    {
        $stmt = db()->prepare("DELETE FROM `{$this->table}` WHERE expires_at < NOW()");
        $stmt->execute();
        return $stmt->rowCount();
    }
}

// Register the handler (only for web requests, not for command line scripts)
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    session_set_save_handler(new DatabaseSessionHandler(), true);
}
