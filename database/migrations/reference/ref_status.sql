-- 1. Cipta jadual ref_status
CREATE TABLE `ref_status` (
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
INSERT INTO
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