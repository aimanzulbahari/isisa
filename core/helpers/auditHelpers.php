<?php
declare(strict_types=1);

/**
 * Audit trail helpers (table: audits).
 *
 * Record WHO changed WHAT and WHEN. Example after updating a user:
 *
 *   audit('UPDATED', 'users', $userId, $oldRow, $newRow);
 */

if (!function_exists('auditHiddenFields')) {
    /** Fields that must never be written into the audit log. */
    function auditHiddenFields(): array
    {
        return ['password', 'password_confirmation', 'csrf_token', 'token'];
    }
}

if (!function_exists('auditDiff')) {
    /**
     * Keep only the fields that really changed.
     *
     * @return array{0: array, 1: array} [old values, new values]
     */
    function auditDiff(array $old, array $new): array
    {
        $oldChanged = $newChanged = [];

        foreach ($new as $field => $value) {
            if (!array_key_exists($field, $old) || (string) $old[$field] !== (string) $value) {
                $oldChanged[$field] = $old[$field] ?? null;
                $newChanged[$field] = $value;
            }
        }
        return [$oldChanged, $newChanged];
    }
}

if (!function_exists('audit')) {
    /**
     * Write one audit row.
     *
     * @param string     $event   CREATED | UPDATED | DELETED | RESTORED (or your own)
     * @param string     $table   Table / model name, e.g. "users"
     * @param int        $id      ID of the record that was changed
     * @param array|null $old     Values before the change (null for CREATED)
     * @param array|null $new     Values after the change (null for DELETED)
     *
     * For UPDATED, only the changed fields are saved. If nothing changed, nothing is saved.
     */
    function audit(string $event, string $table, int $id, ?array $old = null, ?array $new = null): bool
    {
        $event = strtoupper($event);

        // Never store secrets
        $hidden = array_flip(auditHiddenFields());
        $old    = $old !== null ? array_diff_key($old, $hidden) : null;
        $new    = $new !== null ? array_diff_key($new, $hidden) : null;

        if ($event === 'UPDATED' && $old !== null && $new !== null) {
            [$old, $new] = auditDiff($old, $new);
            if ($new === []) {
                return true; // Nothing changed
            }
        }

        try {
            $stmt = db()->prepare(
                'INSERT INTO audits
                    (user_id, event, auditable_type, auditable_id, old_values, new_values, url, ip_address, user_agent)
                 VALUES
                    (:user_id, :event, :type, :id, :old, :new, :url, :ip, :agent)'
            );
            return $stmt->execute([
                'user_id' => currentUserId(),
                'event'   => substr($event, 0, 50),
                'type'    => substr($table, 0, 150),
                'id'      => $id,
                'old'     => $old !== null ? json_encode($old, JSON_UNESCAPED_UNICODE) : null,
                'new'     => $new !== null ? json_encode($new, JSON_UNESCAPED_UNICODE) : null,
                'url'     => PHP_SAPI === 'cli' ? null : currentUrl(),
                'ip'      => clientIp(),
                'agent'   => userAgent() ?: null,
            ]);
        } catch (Throwable $e) {
            // An audit problem should not break the user's action, but must be seen in the log
            error_log('[AUDIT] ' . $e->getMessage());
            return false;
        }
    }
}
