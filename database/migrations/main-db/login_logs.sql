CREATE TABLE `login_logs` (
  `login_log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `username_attempted` VARCHAR(150) NOT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL,
  `failure_reason` VARCHAR(255) NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `device_type` VARCHAR(50) NULL DEFAULT NULL,
  `session_id` VARCHAR(255) NULL DEFAULT NULL,
  `login_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_at` TIMESTAMP NULL DEFAULT NULL,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`login_log_id`),
  INDEX `idx_login_logs_user_id` (`user_id`),
  INDEX `idx_login_logs_username` (`username_attempted`),
  INDEX `idx_login_logs_status` (`ref_status_id`),
  INDEX `idx_login_logs_ip` (`ip_address`),
  INDEX `idx_login_logs_login_at` (`login_at`),

  CONSTRAINT `fk_login_logs_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_login_logs_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;