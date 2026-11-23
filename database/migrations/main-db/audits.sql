CREATE TABLE `audits` (
  `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `event` VARCHAR(50) NOT NULL COMMENT 'Contoh: CREATED, UPDATED, DELETED, RESTORED',
  `auditable_type` VARCHAR(150) NOT NULL COMMENT 'Nama jadual atau model yang terlibat',
  `auditable_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID rekod yang diubah',
  `old_values` JSON NULL DEFAULT NULL COMMENT 'Nilai data sebelum perubahan',
  `new_values` JSON NULL DEFAULT NULL COMMENT 'Nilai data selepas perubahan',
  `url` TEXT NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Primary Key & Indexing
PRIMARY KEY (`audit_id`),
  INDEX `idx_audits_user_id` (`user_id`),
  INDEX `idx_audits_auditable` (`auditable_type`, `auditable_id`),
  INDEX `idx_audits_event` (`event`),
  INDEX `idx_audits_created_at` (`created_at`),

-- Foreign Key: the user who did the action (needs the `users` table to exist first).
-- ON DELETE SET NULL keeps the audit history if that user is deleted later.
  CONSTRAINT `fk_audits_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;