CREATE TABLE `system_info` (
  `system_info_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `system_name` VARCHAR(150) NOT NULL,
  `system_short_name` VARCHAR(50) NULL DEFAULT NULL,
  `system_version` VARCHAR(20) NOT NULL DEFAULT '1.0.0',
  `organization_name` VARCHAR(255) NOT NULL,
  `system_department` INT UNSIGNED NULL DEFAULT NULL,
  `support_email` VARCHAR(150) NULL DEFAULT NULL,
  `support_phone` VARCHAR(50) NULL DEFAULT NULL,
  `logo_path` VARCHAR(255) NULL DEFAULT NULL,
  `agency_logo_path` VARCHAR(255) NULL DEFAULT NULL,
  `copyright_text` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`system_info_id`),
  INDEX `idx_system_info_department` (`system_department`),
  INDEX `idx_system_info_created_by` (`created_by`),
  
  CONSTRAINT `fk_system_info_department` 
    FOREIGN KEY (`system_department`) 
    REFERENCES `ref_department` (`ref_department_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;