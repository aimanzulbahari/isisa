-- 1. Cipta jadual ref_department
CREATE TABLE `ref_department` (
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
INSERT INTO
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