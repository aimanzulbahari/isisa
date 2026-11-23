-- 1. Cipta jadual ref_severity
CREATE TABLE `ref_severity` (
  `ref_severity_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_severity_code` VARCHAR(50) NOT NULL,
  `ref_severity_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_severity_id`),
  UNIQUE INDEX `idx_ref_severity_code` (`ref_severity_code`),
  INDEX `idx_ref_severity_description` (`ref_severity_description`),
  INDEX `idx_ref_severity_status` (`ref_status_id`),
  INDEX `idx_ref_severity_created_by` (`created_by`),

  CONSTRAINT `fk_ref_severity_status`
    FOREIGN KEY (`ref_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data tahap keterukan (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_severity` (
        `ref_severity_code`,
        `ref_severity_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('LOW', 'Rendah', 1, 1),
    ('MEDIUM', 'Sederhana', 1, 1),
    ('HIGH', 'Tinggi', 1, 1),
    ('CRITICAL', 'Kritikal', 1, 1);
