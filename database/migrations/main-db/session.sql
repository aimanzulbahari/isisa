CREATE TABLE `sessions` (
  `session_id` VARCHAR(255) NOT NULL,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT UNSIGNED NOT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

-- Primary Key & Indexing
PRIMARY KEY (`session_id`),
  INDEX `idx_sessions_user_id` (`user_id`),
  INDEX `idx_sessions_last_activity` (`last_activity`),
  INDEX `idx_sessions_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;