-- 1. Cipta jadual ref_announcement_type
CREATE TABLE `ref_announcement_type` (
  `ref_announcement_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_announcement_type_code` VARCHAR(50) NOT NULL,
  `ref_announcement_type_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_announcement_type_id`),
  UNIQUE INDEX `idx_ref_announcement_type_code` (`ref_announcement_type_code`),
  INDEX `idx_ref_announcement_type_description` (`ref_announcement_type_description`),
  INDEX `idx_ref_announcement_type_status` (`ref_status_id`),
  INDEX `idx_ref_announcement_type_created_by` (`created_by`),

  CONSTRAINT `fk_ref_announcement_type_status`
    FOREIGN KEY (`ref_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data jenis pengumuman (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_announcement_type` (
        `ref_announcement_type_code`,
        `ref_announcement_type_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('GENERAL', 'Umum', 1, 1);
