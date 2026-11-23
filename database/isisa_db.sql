-- ==========================================================================
-- ISISA - Database setup (isisa_db)
--
-- All files in database/migrations/, joined in the right order
-- (a table must come AFTER the tables its foreign keys point to).
--
-- Safe to run again: tables use "IF NOT EXISTS" and the starter data uses
-- "INSERT IGNORE", so nothing is deleted or duplicated.
--
-- Order:
--   1. ref_status                                  (used by almost every table)
--   2. ref_role, ref_role_type, ref_department, ref_branch, ref_division,
--      ref_states, ref_file_type, ref_severity,
--      ref_announcement_type, ref_helpdesk_type     (need only ref_status)
--   3. ref_section, system_info                    (need ref_department)
--   4. users                                       (needs the tables above)
--   5. login_logs, audits, sessions, ref_no_counters, helpdesk,
--      announcement, maintenance, data_backup
--   6. Starter user: Superadmin
--   7. Starter maintenance row (needs the Superadmin user)
--
-- All reference migration files now contain data.
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS `isisa_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `isisa_db`;
SET NAMES utf8mb4;

-- --------------------------------------------------------------------------
-- 1. ref_status   (migrations/ref_status.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_status
CREATE TABLE IF NOT EXISTS `ref_status` (
  `ref_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_status_code` VARCHAR(50) NOT NULL,
  `ref_status_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Primary Key & Indexing
PRIMARY KEY (`ref_status_id`),
  UNIQUE INDEX `idx_ref_status_code` (`ref_status_code`),
  INDEX `idx_ref_status_description` (`ref_status_description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data permulaan & status log masuk
INSERT IGNORE INTO
    `ref_status` (
        `ref_status_code`,
        `ref_status_description`
    )
VALUES ('ACTIVE', 'Active'), -- ID: 1
    ('NOT_ACTIVE', 'Not Active'), -- ID: 2
    ('SUCCESS', 'Success'), -- ID: 3
    ('FAILED', 'Failed'), -- ID: 4
    ('LOCKED', 'Locked'), -- ID: 5
    ('TICKET_OPEN', 'Dibuka'), -- ID: 6
    ('TICKET_IN_PROGRESS', 'Dalam Tindakan'), -- ID: 7
    ('TICKET_ON_HOLD', 'Ditangguhkan'), -- ID: 8
    ('TICKET_TRANSFERRED', 'Dipindahkan'), -- ID: 9
    ('TICKET_CLOSED', 'Ditutup'); -- ID: 10

-- --------------------------------------------------------------------------
-- 2. ref_role   (migrations/ref_role.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_role
CREATE TABLE IF NOT EXISTS `ref_role` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_role_type   (migrations/ref_role_type.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_role_type
CREATE TABLE IF NOT EXISTS `ref_role_type` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_department   (migrations/ref_department.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_department
CREATE TABLE IF NOT EXISTS `ref_department` (
  `ref_department_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_department_code` VARCHAR(50) NOT NULL,
  `ref_department_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_department_id`),
  UNIQUE INDEX `idx_ref_department_code` (`ref_department_code`),
  INDEX `idx_ref_department_description` (`ref_department_description`),
  INDEX `idx_ref_department_status` (`ref_status_id`),
  INDEX `idx_ref_department_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_department_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data jabatan berdasarkan gambar (created_by = 1, ref_status_id = 1)
INSERT IGNORE INTO
    `ref_department` (
        `ref_department_code`,
        `ref_department_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES (
        'PDB',
        'Pejabat Datuk Bandar',
        1,
        1
    ),
    (
        'PTDB',
        'Pejabat Timbalan Datuk Bandar',
        1,
        1
    ),
    (
        'JKP',
        'Jabatan Khidmat Pengurusan',
        1,
        1
    ),
    (
        'JKW',
        'Jabatan Kewangan',
        1,
        1
    ),
    (
        'JPPH',
        'Jabatan Penilaian Dan Pengurusan Harta',
        1,
        1
    ),
    (
        'JKT',
        'Jabatan Kejuruteraan',
        1,
        1
    ),
    (
        'JPR',
        'Jabatan Perancangan',
        1,
        1
    ),
    (
        'JLS',
        'Jabatan Landskap',
        1,
        1
    ),
    (
        'JPL',
        'Jabatan Pelesenan',
        1,
        1
    ),
    (
        'JKPS',
        'Jabatan Kesihatan Persekitaran',
        1,
        1
    ),
    (
        'JBG',
        'Jabatan Bangunan',
        1,
        1
    ),
    (
        'JPK',
        'Jabatan Pembangunan Komuniti',
        1,
        1
    ),
    (
        'JPKU',
        'Jabatan Penguatkuasaan',
        1,
        1
    ),
    (
        'JKAKS',
        'Jabatan Kebersihan Awam & Kelestarian Sisa',
        1,
        1
    ),
    (
        'JDTM',
        'Jabatan Digital & Teknologi Maklumat',
        1,
        1
    ),
    (
        'JKPA',
        'Jabatan Korporat & Perhubungan Awam',
        1,
        1
    ),
    (
        'JPU',
        'Jabatan Perundangan',
        1,
        1
    ),
    (
        'JKUB',
        'Jabatan Kontrak & Ukur Bahan',
        1,
        1
    ),
    (
        'JPB',
        'Jabatan Pengangkutan Bandar',
        1,
        1
    );

-- --------------------------------------------------------------------------
-- 2. ref_branch   (migrations/ref_branch.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_branch
CREATE TABLE IF NOT EXISTS `ref_branch` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_division   (migrations/ref_division.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_division
CREATE TABLE IF NOT EXISTS `ref_division` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_states   (migrations/ref_states.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_states
CREATE TABLE IF NOT EXISTS `ref_states` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_file_type   (migrations/ref_file_types.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_file_type
CREATE TABLE IF NOT EXISTS `ref_file_type` (
  `ref_file_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_file_type_code` VARCHAR(50) NOT NULL,
  `ref_file_type_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_file_type_id`),
  UNIQUE INDEX `idx_ref_file_type_code` (`ref_file_type_code`),
  INDEX `idx_ref_file_type_description` (`ref_file_type_description`),
  INDEX `idx_ref_file_type_status` (`ref_status_id`),
  INDEX `idx_ref_file_type_created_by` (`created_by`),
  
  CONSTRAINT `fk_ref_file_type_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan contoh data jenis fail umum (created_by = 1, ref_status_id = 1)
INSERT IGNORE INTO
    `ref_file_type` (
        `ref_file_type_code`,
        `ref_file_type_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES (
        'PDF',
        'Portable Document Format (.pdf)',
        1,
        1
    ),
    (
        'DOCX',
        'Microsoft Word Document (.docx)',
        1,
        1
    ),
    (
        'XLSX',
        'Microsoft Excel Spreadsheet (.xlsx)',
        1,
        1
    ),
    (
        'JPG',
        'JPEG Image (.jpg)',
        1,
        1
    ),
    (
        'PNG',
        'Portable Network Graphics (.png)',
        1,
        1
    );

-- --------------------------------------------------------------------------
-- 2. ref_severity   (migrations/ref_severity.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_severity
CREATE TABLE IF NOT EXISTS `ref_severity` (
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
INSERT IGNORE INTO
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

-- --------------------------------------------------------------------------
-- 2. ref_announcement_type   (migrations/ref_announcement_type.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_announcement_type
CREATE TABLE IF NOT EXISTS `ref_announcement_type` (
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
INSERT IGNORE INTO
    `ref_announcement_type` (
        `ref_announcement_type_code`,
        `ref_announcement_type_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('GENERAL', 'Umum', 1, 1);

-- --------------------------------------------------------------------------
-- 2. ref_helpdesk_type   (migrations/ref_helpdesk_type.sql)
-- --------------------------------------------------------------------------
-- 1. Cipta jadual ref_helpdesk_type
CREATE TABLE IF NOT EXISTS `ref_helpdesk_type` (
  `ref_helpdesk_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_helpdesk_type_code` VARCHAR(50) NOT NULL,
  `ref_helpdesk_type_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`ref_helpdesk_type_id`),
  UNIQUE INDEX `idx_ref_helpdesk_type_code` (`ref_helpdesk_type_code`),
  INDEX `idx_ref_helpdesk_type_description` (`ref_helpdesk_type_description`),
  INDEX `idx_ref_helpdesk_type_status` (`ref_status_id`),
  INDEX `idx_ref_helpdesk_type_created_by` (`created_by`),

  CONSTRAINT `fk_ref_helpdesk_type_status`
    FOREIGN KEY (`ref_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Masukkan data jenis helpdesk (created_by = 1, ref_status_id = 1)
INSERT IGNORE INTO
    `ref_helpdesk_type` (
        `ref_helpdesk_type_code`,
        `ref_helpdesk_type_description`,
        `created_by`,
        `ref_status_id`
    )
VALUES
    ('SYSTEM', 'Sistem', 1, 1);

-- --------------------------------------------------------------------------
-- 3. ref_section   (migrations/ref_section.sql)
-- --------------------------------------------------------------------------
-- Cipta jadual ref_section
CREATE TABLE IF NOT EXISTS `ref_section` (
  `ref_section_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_department_id` INT UNSIGNED NOT NULL,
  `ref_section_code` VARCHAR(50) NOT NULL,
  `ref_section_description` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key, Indexing & Foreign Keys

PRIMARY KEY (`ref_section_id`),
  UNIQUE INDEX `idx_ref_section_code` (`ref_section_code`),
  INDEX `idx_ref_section_department` (`ref_department_id`),
  INDEX `idx_ref_section_description` (`ref_section_description`),
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

-- --------------------------------------------------------------------------
-- 3. system_info   (migrations/system_info.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `system_info` (
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

-- --------------------------------------------------------------------------
-- 4. users   (migrations/users.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
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

-- If the `users` table already existed WITHOUT `office_extension` (the CREATE above skips an
-- existing table), add the column now. Does nothing when the column is already there.
SET @col_office_ext := (
    SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'office_extension'
);
SET @col_office_ext_sql := IF(@col_office_ext = 0,
    'ALTER TABLE `users` ADD COLUMN `office_extension` VARCHAR(20) NULL DEFAULT NULL COMMENT ''Sambungan telefon pejabat'' AFTER `phone_number`',
    'SELECT ''users.office_extension already exists'' AS info');
PREPARE col_office_ext_stmt FROM @col_office_ext_sql;
EXECUTE col_office_ext_stmt;
DEALLOCATE PREPARE col_office_ext_stmt;

-- --------------------------------------------------------------------------
-- 5. login_logs   (migrations/login_logs.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_logs` (
  `login_log_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `username_attempted` VARCHAR(150) NOT NULL,
  `ref_status_id` INT UNSIGNED NOT NULL,
  `failure_reason` VARCHAR(255) NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NOT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `device_type` VARCHAR(50) NULL DEFAULT NULL,
  `session_id` VARCHAR(255) NULL DEFAULT NULL,
  `login_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logout_at` TIMESTAMP NULL DEFAULT NULL,

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`login_log_id`),
  INDEX `idx_login_logs_user_id` (`user_id`),
  INDEX `idx_login_logs_username` (`username_attempted`),
  INDEX `idx_login_logs_status` (`ref_status_id`),
  INDEX `idx_login_logs_ip` (`ip_address`),
  INDEX `idx_login_logs_login_at` (`login_at`),

  CONSTRAINT `fk_login_logs_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_login_logs_status` 
    FOREIGN KEY (`ref_status_id`) 
    REFERENCES `ref_status` (`ref_status_id`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. audits   (migrations/audits.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audits` (
  `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `event` VARCHAR(50) NOT NULL COMMENT 'Contoh: CREATED, UPDATED, DELETED, RESTORED',
  `auditable_type` VARCHAR(150) NOT NULL COMMENT 'Nama jadual atau model yang terlibat',
  `auditable_id` BIGINT UNSIGNED NOT NULL COMMENT 'ID rekod yang diubah',
  `old_values` JSON NULL DEFAULT NULL COMMENT 'Nilai data sebelum perubahan',
  `new_values` JSON NULL DEFAULT NULL COMMENT 'Nilai data selepas perubahan',
  `url` TEXT NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Primary Key & Indexing
PRIMARY KEY (`audit_id`),
  INDEX `idx_audits_user_id` (`user_id`),
  INDEX `idx_audits_auditable` (`auditable_type`, `auditable_id`),
  INDEX `idx_audits_event` (`event`),
  INDEX `idx_audits_created_at` (`created_at`),

-- Foreign Key: the user who did the action (needs the `users` table to exist first).
-- ON DELETE SET NULL keeps the audit history if that user is deleted later.
  CONSTRAINT `fk_audits_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If the `audits` table already existed WITHOUT this foreign key (the line above skips an
-- existing table), add it now. Does nothing when the foreign key is already there.
SET @fk_audits_user := (
    SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'audits' AND CONSTRAINT_NAME = 'fk_audits_user'
);
SET @fk_audits_sql := IF(@fk_audits_user = 0,
    'ALTER TABLE `audits` ADD CONSTRAINT `fk_audits_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`) ON UPDATE CASCADE ON DELETE SET NULL',
    'SELECT ''fk_audits_user already exists'' AS info');
PREPARE fk_audits_stmt FROM @fk_audits_sql;
EXECUTE fk_audits_stmt;
DEALLOCATE PREPARE fk_audits_stmt;

-- --------------------------------------------------------------------------
-- 5. sessions   (migrations/session.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` VARCHAR(255) NOT NULL,
  `user_id` INT UNSIGNED NULL DEFAULT NULL,
  `ip_address` VARCHAR(45) NULL DEFAULT NULL,
  `user_agent` TEXT NULL DEFAULT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT UNSIGNED NOT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

-- Primary Key & Indexing
PRIMARY KEY (`session_id`),
  INDEX `idx_sessions_user_id` (`user_id`),
  INDEX `idx_sessions_last_activity` (`last_activity`),
  INDEX `idx_sessions_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. ref_no_counters   (migrations/ref_no_counters.sql)
-- Running numbers for reference numbers such as ISISA/HD/2026/1/00001
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ref_no_counters` (
  `prefix` VARCHAR(30) NOT NULL COMMENT 'Contoh: ISISA/HD',
  `year` SMALLINT UNSIGNED NOT NULL,
  `last_no` INT UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Primary Key
PRIMARY KEY (`prefix`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. helpdesk   (migrations/helpdesk.sql)
-- --------------------------------------------------------------------------
-- Aduan / tiket Helpdesk.
-- helpdesk_reference_no dijana oleh ref_no_counters (cth: ISISA/HD/2026/1/00001).
CREATE TABLE IF NOT EXISTS `helpdesk` (
  `helpdesk_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `helpdesk_reference_no` VARCHAR(50) NOT NULL COMMENT 'Contoh: ISISA/HD/2026/1/00001',
  `helpdesk_type_id` INT UNSIGNED NOT NULL,
  `helpdesk_severity_id` INT UNSIGNED NOT NULL,
  `helpdesk_title` VARCHAR(255) NOT NULL,
  `helpdesk_description` TEXT NOT NULL,
  `helpdesk_attachment` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Laluan fail lampiran',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `status_id` INT UNSIGNED NOT NULL DEFAULT 6 COMMENT 'ref_status: TICKET_OPEN, TICKET_IN_PROGRESS, ...',

-- Primary Key, Indexing & Foreign Key
PRIMARY KEY (`helpdesk_id`),
  UNIQUE INDEX `idx_helpdesk_reference_no` (`helpdesk_reference_no`),
  INDEX `idx_helpdesk_type` (`helpdesk_type_id`),
  INDEX `idx_helpdesk_severity` (`helpdesk_severity_id`),
  INDEX `idx_helpdesk_title` (`helpdesk_title`),
  INDEX `idx_helpdesk_status` (`status_id`),
  INDEX `idx_helpdesk_created_by` (`created_by`),
  INDEX `idx_helpdesk_updated_by` (`updated_by`),
  INDEX `idx_helpdesk_created_at` (`created_at`),

  CONSTRAINT `fk_helpdesk_type`
    FOREIGN KEY (`helpdesk_type_id`)
    REFERENCES `ref_helpdesk_type` (`ref_helpdesk_type_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_severity`
    FOREIGN KEY (`helpdesk_severity_id`)
    REFERENCES `ref_severity` (`ref_severity_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_helpdesk_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_helpdesk_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. announcement   (migrations/announcement.sql)
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `announcement` (
  `announcement_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `announcement_title` VARCHAR(255) NOT NULL,
  `announcement_type_id` INT UNSIGNED NOT NULL,
  `announcement_description` TEXT NULL DEFAULT NULL,
  `announcement_start_date` DATETIME NOT NULL COMMENT 'Pengumuman mula dipaparkan',
  `announcement_end_date` DATETIME NULL DEFAULT NULL COMMENT 'Pengumuman tamat dipaparkan (NULL = tiada tarikh tamat)',
  `announcement_status_id` INT UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,

-- Primary Key & Indexing
PRIMARY KEY (`announcement_id`),
  INDEX `idx_announcement_title` (`announcement_title`),
  INDEX `idx_announcement_type` (`announcement_type_id`),
  INDEX `idx_announcement_status` (`announcement_status_id`),
  INDEX `idx_announcement_dates` (`announcement_start_date`, `announcement_end_date`),
  INDEX `idx_announcement_created_by` (`created_by`),
  INDEX `idx_announcement_updated_by` (`updated_by`),

-- Foreign Keys
  CONSTRAINT `fk_announcement_type`
    FOREIGN KEY (`announcement_type_id`)
    REFERENCES `ref_announcement_type` (`ref_announcement_type_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_announcement_status`
    FOREIGN KEY (`announcement_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_announcement_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_announcement_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

-- The end date can not be before the start date
  CONSTRAINT `chk_announcement_dates`
    CHECK (`announcement_end_date` IS NULL OR `announcement_end_date` >= `announcement_start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. maintenance   (migrations/maintenance.sql)
-- Penyelenggaraan (maintenance mode). Needs: ref_status and users.
-- --------------------------------------------------------------------------
-- Satu baris tetapan sahaja (maintenance_id = 1); borang "Kemaskini Penyelenggaraan" mengemas kini baris ini.
--
-- Pemetaan medan borang:
--   Set Status Penyelenggaraan        -> maintenance_status_id (ref_status: 1 = Penyelenggaraan Aktif, 2 = Penyelenggaraan Tidak Aktif)
--   Tarikh & Masa Mula                -> maintenance_start_date
--   Tarikh & Masa Tamat               -> maintenance_end_date
--   Pegawai Yang Bertanggungjawab     -> maintenance_officer_id (pengguna yang menyimpan; dirakam automatik)
--   Mesej Penyelenggaraan             -> maintenance_message (maks. 500 aksara)
--   Peranan Yang Boleh Akses Sistem   -> enam lajur allow_* (satu bagi setiap peranan ref_role; 1 = boleh masuk semasa penyelenggaraan)
-- Kotak akuan "Saya mengesahkan ..." hanya semakan borang dan tidak disimpan.
-- Tarikh mula/tamat dan pegawai boleh NULL supaya baris awal (Tidak Aktif) sah;
-- borang mewajibkan tarikh apabila status Aktif disimpan.
CREATE TABLE IF NOT EXISTS `maintenance` (
  `maintenance_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `maintenance_start_date` DATETIME NULL DEFAULT NULL COMMENT 'Tarikh & masa penyelenggaraan mula',
  `maintenance_end_date` DATETIME NULL DEFAULT NULL COMMENT 'Tarikh & masa penyelenggaraan tamat',
  `maintenance_officer_id` INT UNSIGNED NULL DEFAULT NULL COMMENT 'Pegawai yang bertanggungjawab (users)',
  `maintenance_message` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Mesej dipaparkan kepada pengguna semasa penyelenggaraan',
  `allow_superadmin` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Superadmin (ref_role_id 1) boleh akses',
  `allow_admin` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Admin / Pentadbir (ref_role_id 2) boleh akses',
  `allow_users` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Users / Pengguna (ref_role_id 3) boleh akses',
  `allow_guest` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Guest / Tetamu (ref_role_id 4) boleh akses',
  `allow_reviewer` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Reviewer / Penyemak (ref_role_id 5) boleh akses',
  `allow_approver` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Approver / Pelulus (ref_role_id 6) boleh akses',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `maintenance_status_id` INT UNSIGNED NOT NULL DEFAULT 2 COMMENT '1 = Penyelenggaraan Aktif, 2 = Penyelenggaraan Tidak Aktif (bertukar automatik kepada 2 apabila tarikh tamat berlalu)',

-- Primary Key & Indexing
PRIMARY KEY (`maintenance_id`),
  INDEX `idx_maintenance_status` (`maintenance_status_id`),
  INDEX `idx_maintenance_dates` (`maintenance_start_date`, `maintenance_end_date`),
  INDEX `idx_maintenance_officer` (`maintenance_officer_id`),
  INDEX `idx_maintenance_created_by` (`created_by`),
  INDEX `idx_maintenance_updated_by` (`updated_by`),

-- Foreign Keys
  CONSTRAINT `fk_maintenance_status`
    FOREIGN KEY (`maintenance_status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_maintenance_officer`
    FOREIGN KEY (`maintenance_officer_id`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_maintenance_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_maintenance_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

-- Tarikh tamat tidak boleh sebelum tarikh mula
  CONSTRAINT `chk_maintenance_dates`
    CHECK (`maintenance_end_date` IS NULL OR `maintenance_start_date` IS NULL OR `maintenance_end_date` >= `maintenance_start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 5. data_backup   (migrations/data_backup.sql)
-- Data backup records. Needs: ref_status and users.
-- --------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `data_backup` (
  `data_backup_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `data_backup_code` VARCHAR(50) NOT NULL COMMENT 'Kod unik sandaran',
  `data_backup_name` VARCHAR(255) NOT NULL COMMENT 'Nama sandaran',
  `file_path` VARCHAR(255) NOT NULL COMMENT 'Laluan fail sandaran',
  `file_size` BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Saiz fail dalam bait',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` INT UNSIGNED NULL DEFAULT NULL,
  `updated_by` INT UNSIGNED NULL DEFAULT NULL,
  `status_id` INT UNSIGNED NOT NULL DEFAULT 1,

-- Primary Key & Indexing
PRIMARY KEY (`data_backup_id`),
  UNIQUE INDEX `idx_data_backup_code` (`data_backup_code`),
  INDEX `idx_data_backup_name` (`data_backup_name`),
  INDEX `idx_data_backup_status` (`status_id`),
  INDEX `idx_data_backup_created_by` (`created_by`),
  INDEX `idx_data_backup_updated_by` (`updated_by`),
  INDEX `idx_data_backup_created_at` (`created_at`),

-- Foreign Keys
  CONSTRAINT `fk_data_backup_status`
    FOREIGN KEY (`status_id`)
    REFERENCES `ref_status` (`ref_status_id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT,

  CONSTRAINT `fk_data_backup_created_by`
    FOREIGN KEY (`created_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL,

  CONSTRAINT `fk_data_backup_updated_by`
    FOREIGN KEY (`updated_by`)
    REFERENCES `users` (`users_id`)
    ON UPDATE CASCADE
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------------
-- 6. Starter user: SUPERADMIN
--
--   Login (IC number) : 010101010101        <- placeholder IC, change it later
--   Password          : set at setup time. Only its hash is stored below, never the password.
--
--   CHANGE THE PASSWORD after the first login (it was shared in a chat).
--   To set a new one: make a hash with
--     php -r "echo password_hash('YourPasswordHere', PASSWORD_DEFAULT);"
--   then run:  UPDATE `users` SET `password` = 'THE_HASH' WHERE `users_id` = 1;
--
--   users_id = 1 on purpose: the starter rows of the ref_* tables use created_by = 1.
--   The role and the status are looked up by CODE, so this still works if the IDs change.
-- --------------------------------------------------------------------------
INSERT IGNORE INTO `users` (
    `users_id`, `myID`, `fullname`, `dob`, `age`,
    `ref_role_id`, `email`, `password`, `created_by`, `ref_status_id`
)
VALUES (
    1,
    '010605100717',
    'Superadmin ISISA',
    '2001-01-01',
    TIMESTAMPDIFF(YEAR, '2001-01-01', CURDATE()),
    (SELECT `ref_role_id` FROM `ref_role` WHERE `ref_role_code` = 'SUPERADMIN'),
    'superadmin@isisa.local',
    '$2y$10$D9Yb1dJ05naDgW65UbM2JutYI75N0pCxYQxtFBCzmszHPUEutHBI.',  -- password hash only (the password itself is never stored)
    1,
    (SELECT `ref_status_id` FROM `ref_status` WHERE `ref_status_code` = 'ACTIVE')
);

-- --------------------------------------------------------------------------
-- 7. Starter maintenance row (after the starter user, because created_by = 1)
-- --------------------------------------------------------------------------
-- Data awal: satu baris tetapan, Penyelenggaraan Tidak Aktif (status 2), hanya Superadmin dibenarkan (allow_* lain = 0).
INSERT IGNORE INTO
    `maintenance` (
        `maintenance_id`,
        `maintenance_status_id`,
        `created_by`
    )
VALUES (1, 2, 1);
