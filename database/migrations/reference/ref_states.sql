-- 1. Cipta jadual ref_states
CREATE TABLE `ref_states` (
  `ref_state_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_state_code` VARCHAR(50) NOT NULL,
  `ref_state_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_state_id`),
  UNIQUE INDEX `idx_ref_state_code` (`ref_state_code`),
  INDEX `idx_ref_state_description` (`ref_state_description`),
  INDEX `idx_ref_state_status` (`ref_status_id`),
  INDEX `idx_ref_state_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_state_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data negeri & wilayah persekutuan berdasarkan gambar (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_states` (
        `ref_state_code`,
        `ref_state_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES ('JHR', 'Johor', 1, 1),
    ('KDH', 'Kedah', 1, 1),
    ('KTN', 'Kelantan', 1, 1),
    ('MLK', 'Melaka', 1, 1),
    (
        'NSN',
        'Negeri Sembilan',
        1,
        1
    ),
    ('PHG', 'Pahang', 1, 1),
    ('PRK', 'Perak', 1, 1),
    ('PLS', 'Perlis', 1, 1),
    ('PNG', 'Pulau Pinang', 1, 1),
    ('SGR', 'Selangor', 1, 1),
    ('TRG', 'Terengganu', 1, 1),
    (
        'KUL',
        'Wilayah Persekutuan Kuala Lumpur',
        1,
        1
    ),
    (
        'PJY',
        'Wilayah Persekutuan Putrajaya',
        1,
        1
    ),
    ('SBH', 'Sabah', 1, 1),
    ('SWK', 'Sarawak', 1, 1),
    (
        'LBN',
        'Wilayah Persekutuan Labuan',
        1,
        1
    );