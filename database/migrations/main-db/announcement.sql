-- Pengumuman (announcements)
-- Needs: ref_announcement_type, ref_status and users (create them first).
CREATE TABLE IF NOT EXISTS `announcement` (
  `announcement_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `announcement_title` VARCHAR(255) NOT NULL,
  `announcement_type_id` INT UNSIGNED NOT NULL,
  `announcement_description` TEXT NULL DEFAULT NULL,
  `announcement_start_date` DATETIME NOT NULL COMMENT 'Pengumuman mula dipaparkan',
  `announcement_end_date` DATETIME NULL DEFAULT NULL COMMENT 'Pengumuman tamat dipaparkan (NULL = tiada tarikh tamat)',
  `announcement_status_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,

-- Primary Key & Indexing
PRIMARY KEY (`announcement_id`),
  INDEX `idx_announcement_title` (`announcement_title`),
  INDEX `idx_announcement_type` (`announcement_type_id`),
  INDEX `idx_announcement_status` (`announcement_status_id`),
  INDEX `idx_announcement_dates` (`announcement_start_date`, `announcement_end_date`),
  INDEX `idx_announcement_created_by` (`created_by`),
  INDEX `idx_announcement_updated_by` (`updated_by`),

-- Foreign Keys
  CONSTRAINT `fk_announcement_type`
    FOREIGN KEY (`announcement_type_id`)
    REFERENCES `ref_announcement_type` (`ref_announcement_type_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_announcement_status`
    FOREIGN KEY (`announcement_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_announcement_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_announcement_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

-- The end date can not be before the start date
  CONSTRAINT `chk_announcement_dates`
    CHECK (`announcement_end_date` IS NULL OR `announcement_end_date` >= `announcement_start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
