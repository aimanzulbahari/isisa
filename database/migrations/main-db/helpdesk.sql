-- Aduan / tiket Helpdesk.
-- helpdesk_reference_no dijana oleh ref_no_counters (cth: ISISA/HD/2026/1/00001).
CREATE TABLE IF NOT EXISTS `helpdesk` (
  `helpdesk_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `helpdesk_reference_no` VARCHAR(50) NOT NULL COMMENT 'Contoh: ISISA/HD/2026/1/00001',
  `helpdesk_type_id` INT UNSIGNED NOT NULL,
  `helpdesk_severity_id` INT UNSIGNED NOT NULL,
  `helpdesk_title` VARCHAR(255) NOT NULL,
  `helpdesk_description` TEXT NOT NULL,
  `helpdesk_attachment` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Laluan fail lampiran',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `status_id` INT UNSIGNED NOT NULL DEFAULT 6 COMMENT 'ref_status: TICKET_OPEN, TICKET_IN_PROGRESS, ...',

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`helpdesk_id`),
  UNIQUE INDEX `idx_helpdesk_reference_no` (`helpdesk_reference_no`),
  INDEX `idx_helpdesk_type` (`helpdesk_type_id`),
  INDEX `idx_helpdesk_severity` (`helpdesk_severity_id`),
  INDEX `idx_helpdesk_title` (`helpdesk_title`),
  INDEX `idx_helpdesk_status` (`status_id`),
  INDEX `idx_helpdesk_created_by` (`created_by`),
  INDEX `idx_helpdesk_updated_by` (`updated_by`),
  INDEX `idx_helpdesk_created_at` (`created_at`),

  CONSTRAINT `fk_helpdesk_type`
    FOREIGN KEY (`helpdesk_type_id`)
    REFERENCES `ref_helpdesk_type` (`ref_helpdesk_type_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_severity`
    FOREIGN KEY (`helpdesk_severity_id`)
    REFERENCES `ref_severity` (`ref_severity_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_helpdesk_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
