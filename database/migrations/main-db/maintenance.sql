-- Penyelenggaraan (maintenance mode)
-- Needs: ref_status and users (create them first).
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

-- Data awal: satu baris tetapan, Penyelenggaraan Tidak Aktif (status 2), hanya Superadmin dibenarkan (allow_* lain = 0).
INSERT INTO
    `maintenance` (
        `maintenance_id`,
        `maintenance_status_id`,
        `created_by`
    )
VALUES (1, 2, 1);

