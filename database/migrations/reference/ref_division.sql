-- 1. Cipta jadual ref_division
CREATE TABLE `ref_division` (
  `ref_division_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_division_code` VARCHAR(50) NOT NULL,
  `ref_division_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_division_id`),
  UNIQUE INDEX `idx_ref_division_code` (`ref_division_code`),
  INDEX `idx_ref_division_description` (`ref_division_description`),
  INDEX `idx_ref_division_status` (`ref_status_id`),
  INDEX `idx_ref_division_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_division_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data bahagian berdasarkan gambar (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_division` (
        `ref_division_code`,
        `ref_division_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES (
        'BAD',
        'Bahagian Audit Dalam',
        1,
        1
    ),
    (
        'COB',
        'Bahagian Pesuruhjaya Bangunan (COB)',
        1,
        1
    ),
    (
        'OSC',
        'Bahagian Pusat Setempat (OSC)',
        1,
        1
    ),
    (
        'BI',
        'Bahagian Integriti',
        1,
        1
    );