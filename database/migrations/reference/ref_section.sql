-- Cipta jadual ref_section
CREATE TABLE `ref_section` (
  `ref_section_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_department_id` INT UNSIGNED NOT NULL,
  `ref_section_code` VARCHAR(50) NOT NULL,
  `ref_section_name` VARCHAR(255) NOT NULL COMMENT 'Nama seksyen',
  `ref_section_description` VARCHAR(300) NULL DEFAULT NULL COMMENT 'Keterangan seksyen (textarea popup Tambah Seksyen)',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Keys

PRIMARY KEY (`ref_section_id`),
  UNIQUE INDEX `idx_ref_section_code` (`ref_section_code`),
  INDEX `idx_ref_section_department` (`ref_department_id`),
  INDEX `idx_ref_section_name` (`ref_section_name`),
  INDEX `idx_ref_section_status` (`ref_status_id`),
  INDEX `idx_ref_section_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_section_department` 
    FOREIGN KEY (`ref_department_id`) 
    REFERENCES `ref_department` (`ref_department_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,

  CONSTRAINT `fk_ref_section_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;