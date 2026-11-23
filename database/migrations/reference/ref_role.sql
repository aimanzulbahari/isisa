-- 1. Cipta jadual ref_role
CREATE TABLE `ref_role` (
  `ref_role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_role_code` VARCHAR(50) NOT NULL,
  `ref_role_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key

PRIMARY KEY (`ref_role_id`),
  UNIQUE INDEX `idx_ref_role_code` (`ref_role_code`),
  INDEX `idx_ref_role_description` (`ref_role_description`),
  INDEX `idx_ref_role_status` (`ref_status_id`),
  INDEX `idx_ref_role_created_by` (`created_by`),

  CONSTRAINT `fk_ref_role_status`
    FOREIGN KEY (`ref_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data peranan (created_by = 1, ref_status_id = 1)
INSERT INTO
    `ref_role` (
        `ref_role_code`,
        `ref_role_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('SUPERADMIN', 'Superadmin', 1, 1),
    ('ADMIN', 'Pentadbir', 1, 1),
    ('USERS', 'Pengguna', 1, 1),
    ('GUEST', 'Tetamu', 1, 1),
    ('REVIEWER', 'Penyemak', 1, 1),
    ('APPROVER', 'Pelulus', 1, 1);
