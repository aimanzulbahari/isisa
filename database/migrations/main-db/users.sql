CREATE TABLE `users` (
  `users_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `myID` VARCHAR(20) NOT NULL COMMENT 'IC Number (contoh: 900101105544)',
  `fullname` VARCHAR(255) NOT NULL,
  `dob` DATE NULL DEFAULT NULL COMMENT 'Tarikh lahir diekstrak daripada myID',
  `age` TINYINT UNSIGNED NULL DEFAULT NULL COMMENT 'Umur dikira secara automatik',
  `ref_role_id` INT UNSIGNED NOT NULL,
  `ref_role_type_id` INT UNSIGNED NULL DEFAULT NULL,
  `ref_department_id` INT UNSIGNED NULL DEFAULT NULL,
  `ref_branch_id` INT UNSIGNED NULL DEFAULT NULL,
  `ref_division_id` INT UNSIGNED NULL DEFAULT NULL,
  `ref_section_id` INT UNSIGNED NULL DEFAULT NULL,
  `position` VARCHAR(150) NULL DEFAULT NULL COMMENT 'Jawatan pengguna (contoh: Pegawai Kesihatan Persekitaran)',
  `phone_number` VARCHAR(20) NULL DEFAULT NULL,
  `office_extension` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Sambungan telefon pejabat',
  `email` VARCHAR(150) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key & Indexing
PRIMARY KEY (`users_id`),
UNIQUE INDEX `idx_users_myID` (`myID`),
UNIQUE INDEX `idx_users_email` (`email`),
INDEX `idx_users_fullname` (`fullname`),
INDEX `idx_users_role` (`ref_role_id`),
INDEX `idx_users_role_type` (`ref_role_type_id`),
INDEX `idx_users_department` (`ref_department_id`),
INDEX `idx_users_branch` (`ref_branch_id`),
INDEX `idx_users_division` (`ref_division_id`),
INDEX `idx_users_section` (`ref_section_id`),
INDEX `idx_users_status` (`ref_status_id`),
INDEX `idx_users_created_by` (`created_by`),

-- Foreign Keys
CONSTRAINT `fk_users_role` 
    FOREIGN KEY (`ref_role_id`) 
    REFERENCES `ref_role` (`ref_role_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,

  CONSTRAINT `fk_users_role_type` 
    FOREIGN KEY (`ref_role_type_id`) 
    REFERENCES `ref_role_type` (`ref_role_type_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,

  CONSTRAINT `fk_users_department` 
    FOREIGN KEY (`ref_department_id`) 
    REFERENCES `ref_department` (`ref_department_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,

  CONSTRAINT `fk_users_branch` 
    FOREIGN KEY (`ref_branch_id`) 
    REFERENCES `ref_branch` (`ref_branch_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,

  CONSTRAINT `fk_users_division` 
    FOREIGN KEY (`ref_division_id`) 
    REFERENCES `ref_division` (`ref_division_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,

  CONSTRAINT `fk_users_section` 
    FOREIGN KEY (`ref_section_id`) 
    REFERENCES `ref_section` (`ref_section_id`) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL,

  CONSTRAINT `fk_users_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;