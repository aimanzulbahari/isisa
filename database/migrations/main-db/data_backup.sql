-- Data backup (rekod sandaran data)
-- Needs: ref_status and users (create them first).
CREATE TABLE IF NOT EXISTS `data_backup` (
  `data_backup_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `data_backup_code` VARCHAR(50) NOT NULL COMMENT 'Kod unik sandaran',
  `data_backup_name` VARCHAR(255) NOT NULL COMMENT 'Nama sandaran',
  `file_path` VARCHAR(255) NOT NULL COMMENT 'Laluan fail sandaran',
  `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Saiz fail dalam bait',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key & Indexing
PRIMARY KEY (`data_backup_id`),
  UNIQUE INDEX `idx_data_backup_code` (`data_backup_code`),
  INDEX `idx_data_backup_name` (`data_backup_name`),
  INDEX `idx_data_backup_status` (`status_id`),
  INDEX `idx_data_backup_created_by` (`created_by`),
  INDEX `idx_data_backup_updated_by` (`updated_by`),
  INDEX `idx_data_backup_created_at` (`created_at`),

-- Foreign Keys
  CONSTRAINT `fk_data_backup_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_data_backup_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_data_backup_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
