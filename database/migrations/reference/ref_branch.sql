-- 1. Cipta jadual ref_branch
CREATE TABLE `ref_branch` (
  `ref_branch_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_branch_code` VARCHAR(50) NOT NULL,
  `ref_branch_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_branch_id`),
  UNIQUE INDEX `idx_ref_branch_code` (`ref_branch_code`),
  INDEX `idx_ref_branch_description` (`ref_branch_description`),
  INDEX `idx_ref_branch_status` (`ref_status_id`),
  INDEX `idx_ref_branch_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_branch_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data cawangan berdasarkan gambar (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_branch` (
        `ref_branch_code`,
        `ref_branch_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES (
        'PCSB',
        'Pejabat Cawangan Sungai Buloh',
        1,
        1
    ),
    (
        'PCKK',
        'Pejabat Cawangan Kota Kemuning',
        1,
        1
    ),
    (
        'PCSA',
        'Pejabat Cawangan Setia Alam',
        1,
        1
    );