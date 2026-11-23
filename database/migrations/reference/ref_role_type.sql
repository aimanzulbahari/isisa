-- 1. Cipta jadual ref_role_type
CREATE TABLE `ref_role_type` (
  `ref_role_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_role_type_code` VARCHAR(50) NOT NULL,
  `ref_role_type_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_role_type_id`),
  UNIQUE INDEX `idx_ref_role_type_code` (`ref_role_type_code`),
  INDEX `idx_ref_role_type_description` (`ref_role_type_description`),
  INDEX `idx_ref_role_type_status` (`ref_status_id`),
  INDEX `idx_ref_role_type_created_by` (`created_by`),

  CONSTRAINT `fk_ref_role_type_status`
    FOREIGN KEY (`ref_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data jenis peranan (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_role_type` (
        `ref_role_type_code`,
        `ref_role_type_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('DIRECTOR', 'Pengarah', 1, 1),
    ('DEPUTY_DIRECTOR', 'Timbalan Pengarah', 1, 1),
    ('ASSISTANT_DIRECTOR', 'Penolong Pengarah', 1, 1),
    ('OFFICER', 'Pegawai', 1, 1),
    ('ASSISTANT_OFFICER', 'Penolong Pegawai', 1, 1),
    ('ADMINISTRATIVE_ASSISTANT', 'Penolong Tadbir', 1, 1),
    ('CONTRACTOR', 'Kontraktor', 1, 1);
