# ISISA – Panduan Pembangun

Panduan ringkas: cara pasang, guna `.env`, tambah modul dan buat jadual database.

## 1. Pasang & jalankan

1. Letak projek di `C:\xampp\htdocs\isisa`, hidupkan **Apache** dan **MySQL** di XAMPP.
2. Pasang dependency:
   ```bash
   composer install
   npm install
   ```
3. Salin `.env.example` kepada `.env` (lihat bahagian 2).
4. Import `database/isisa_db.sql` melalui phpMyAdmin atau DBeaver. Fail ini cipta database `isisa_db` dan jadual asas. Selamat dijalankan semula.
5. Buka `http://localhost/isisa`.

## 2. Guna `.env`

`.env` menyimpan tetapan setiap persekitaran (tidak di-commit). Jangan letak kata laluan sebenar dalam `.env.example`.

```bash
copy .env.example .env
```

| Kumpulan | Contoh kunci |
| --- | --- |
| Aplikasi | `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_MAINTENANCE` |
| Database | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` |
| Sesi & keselamatan | `SESSION_IDLE_TIMEOUT`, `LOGIN_MAX_ATTEMPTS` |
| E-mel (SMTP) | `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` |

Baca nilai dalam kod:

```php
env('DB_HOST', '127.0.0.1');   // terus dari .env, dengan nilai lalai
config('database.host');       // tetapan yang sudah disusun (core/config/*.php)
```

> Di production: `APP_DEBUG=false` dan `SESSION_SECURE_COOKIE=true` (jika guna HTTPS).

## 3. Struktur projek

| Folder | Kegunaan |
| --- | --- |
| `app/auth/` | Halaman log masuk, daftar, lupa kata laluan |
| `MyModules/` | Halaman modul sistem (selepas log masuk) |
| `core/` | Config, helper, middleware (dimuat automatik) |
| `routes/` | `web.php` (URL) dan `breadcrumbs-route.php` |
| `src/` | Layout dan komponen (sidebar, header, breadcrumb) |
| `public/assets/` | CSS dan JS |
| `database/` | `isisa_db.sql` dan `migrations/` |

## 4. Tambah modul baharu

Setiap halaman ada **3 fail** dalam satu folder di `MyModules/`:

```
MyModules/Nama/Halaman/
├── Halaman.php            # paparan (HTML)
├── HalamanController.php  # logic: validasi, simpan, audit
└── HalamanModel.php       # query database sahaja
```

Contoh terbaik untuk ditiru: `MyModules/Management/Wisma/Department/`.

**Langkah:**

1. Salin folder contoh, tukar nama fail dan nama kelas/fungsi.
2. Daftar route dalam `routes/web.php`:
   ```php
   '/Nama/Halaman' => [
       'name'  => 'nama-halaman',
       'file'  => 'MyModules/Nama/Halaman/Halaman.php',
       'guard' => 'auth',   // guest | auth | role:ADMIN,SUPERADMIN | public
   ],
   ```
3. Tambah breadcrumb dalam `routes/breadcrumbs-route.php`.
4. Tambah menu dalam `src/layout/sidebar.php` **dan** `sidebar-mobile.php` (kekalkan sama), kemudian daftar kunci dalam `core/helpers/menuHelpers.php` supaya peranan yang betul sahaja nampak menu itu.
5. Pautan sentiasa guna nama route: `routeUrl('nama-halaman')`, bukan URL tetap.

## 5. Buat jadual database

1. Tulis fail SQL baharu dalam `database/migrations/main-db/` (data rujukan: `reference/`):
   ```sql
   CREATE TABLE IF NOT EXISTS `contoh` (
     `contoh_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
     `nama` VARCHAR(255) NOT NULL,
     `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `created_by` INT UNSIGNED NULL DEFAULT NULL,
     `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
     `updated_by` INT UNSIGNED NULL DEFAULT NULL,
     `ref_status_id` INT UNSIGNED NOT NULL DEFAULT 1,
     PRIMARY KEY (`contoh_id`),
     CONSTRAINT `fk_contoh_status` FOREIGN KEY (`ref_status_id`)
       REFERENCES `ref_status` (`ref_status_id`)
       ON UPDATE CASCADE ON DELETE RESTRICT
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   ```
2. Tampal kandungan yang sama ke `database/isisa_db.sql` **selepas** jadual yang dirujuk oleh foreign key.
3. Gunakan `CREATE TABLE IF NOT EXISTS` dan `INSERT IGNORE` supaya selamat dijalankan semula.

## 6. Peraturan menulis kod

- Setiap fail PHP bermula dengan `declare(strict_types=1);`.
- **Model**: query database sahaja. **Controller**: validasi, simpan, audit, redirect. **Paparan**: HTML sahaja.
- Query sentiasa guna **prepared statement** (`db()->prepare(...)`), jangan gabung input terus ke SQL.
- Output ke HTML sentiasa guna `e($nilai)`.
- Borang POST: letak `<?= csrfField() ?>`, dan selepas simpan **redirect** (Post/Redirect/Get) guna `setFlash()` untuk mesej.
- Rekod setiap tindakan dengan `audit('CREATED', 'nama_jadual', $id, $lama, $baru)`.
- Bandingkan peranan dengan **kod** (`hasRole('SUPERADMIN')`), bukan ID. Lindungi halaman dengan `guard` pada route atau `requireRole()`.
- Menyembunyikan menu hanyalah paparan; keselamatan sebenar di server.
