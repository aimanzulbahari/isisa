-- Running numbers for reference numbers (see app/api/refNo/helpdeskRefNo.php).
-- One row per prefix + year. `last_no` is the last number that was given out.
-- The number is taken with one atomic statement, so two users at the same time never get the same one.
CREATE TABLE IF NOT EXISTS `ref_no_counters` (
  `prefix` VARCHAR(30) NOT NULL COMMENT 'Contoh: ISISA/HD',
  `year` SMALLINT UNSIGNED NOT NULL,
  `last_no` INT UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

-- Primary Key
PRIMARY KEY (`prefix`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
