<?php
declare(strict_types=1);

/**
 * System branding: name, logo, organisation and copyright of the system.
 *
 * The data comes from the table `system_info` (one row only), which is edited on the
 * "Info Sistem" page (MyModules/Management/System/Info). If the row or table is missing,
 * or the database is down, safe default values are used, so a page (even the login page)
 * never breaks because of branding.
 *
 * Usage (include once, then call the function):
 *
 *   require_once SRC_PATH . '/components/system-branding.php';
 *   $brand = systemBranding();
 *   echo e($brand['title']);        // "ISISA"
 *   echo e($brand['subtitle']);     // "Majlis Bandaraya Shah Alam"
 *   echo e(asset($brand['logo_url'])) ... or use $brand['logo_url'] directly (already a full URL)
 *
 * The result is read from the database once per request.
 *
 * Keys of the result:
 *   title            short name if set, else the full system name   (for the logo area / header)
 *   name             full system name
 *   short_name       short name (falls back to name)
 *   version          e.g. "1.0.0"
 *   organization     organisation name
 *   department_id    ref_department_id, or null
 *   support_email    or null
 *   support_phone    or null
 *   logo_url         system logo (full URL; the default emblem if none was uploaded)
 *   agency_logo_url  agency logo (full URL; falls back to logo_url)
 *   copyright        copyright text
 *   subtitle         same as organization (for the logo area)
 *   from_database    true when the values came from system_info, false when they are defaults
 *
 * Everything returned is plain text: escape it with e() when you print it.
 */

if (!function_exists('systemBrandingDefaults')) {
    /** Values used when nothing is saved in system_info yet. */
    function systemBrandingDefaults(): array
    {
        $organization = 'Majlis Bandaraya Shah Alam';

        return [
            'name'            => 'ISISA',
            'short_name'      => 'ISISA',
            'version'         => '1.0.0',
            'organization'    => $organization,
            'department_id'   => null,
            'support_email'   => null,
            'support_phone'   => null,
            'logo_path'       => null,
            'agency_logo_path' => null,
            'copyright'       => '© ' . date('Y') . ' ' . $organization,
        ];
    }
}

if (!function_exists('systemBrandingDefaultLogo')) {
    /** The emblem shown when no logo has been uploaded. */
    function systemBrandingDefaultLogo(): string
    {
        return 'https://upload.wikimedia.org/wikipedia/commons/e/e9/Shah_Alam_Emblem.svg';
    }
}

if (!function_exists('systemBrandingLogoUrl')) {
    /**
     * Full URL of an uploaded logo, or $fallback.
     * Only files that really exist inside public/uploads/system are accepted, so a wrong or
     * edited value in the database can never point the page to another file.
     */
    function systemBrandingLogoUrl(?string $path, string $fallback): string
    {
        $path = trim((string) $path);

        if (
            $path === ''
            || !str_starts_with($path, 'uploads/system/')
            || str_contains($path, '..')
            || str_contains($path, '\\')
            || !is_file(PUBLIC_PATH . '/' . $path)
        ) {
            return $fallback;
        }
        return asset($path, true);
    }
}

if (!function_exists('systemBranding')) {
    /**
     * The system branding (see the top of this file for the keys).
     *
     * @param bool $refresh Read the database again (use it right after saving Info Sistem).
     * @return array<string,mixed>
     */
    function systemBranding(bool $refresh = false): array
    {
        static $brand = null;

        if ($brand !== null && !$refresh) {
            return $brand;
        }

        $values       = systemBrandingDefaults();
        $fromDatabase = false;

        try {
            $row = db()->query('SELECT * FROM system_info ORDER BY system_info_id ASC LIMIT 1')->fetch();

            if (is_array($row)) {
                $fromDatabase = true;

                // A saved text replaces the default only when it is not empty
                $text = static fn (string $key, mixed $default): mixed
                    => trim((string) ($row[$key] ?? '')) !== '' ? trim((string) $row[$key]) : $default;

                $values['name']             = $text('system_name', $values['name']);
                $values['short_name']       = $text('system_short_name', $values['name']);
                $values['version']          = $text('system_version', $values['version']);
                $values['organization']     = $text('organization_name', $values['organization']);
                $values['department_id']    = isset($row['system_department']) ? (int) $row['system_department'] : null;
                $values['support_email']    = $text('support_email', null);
                $values['support_phone']    = $text('support_phone', null);
                $values['logo_path']        = $text('logo_path', null);
                $values['agency_logo_path'] = $text('agency_logo_path', null);
                $values['copyright']        = $text('copyright_text', '© ' . date('Y') . ' ' . $values['organization']);
            }
        } catch (Throwable $e) {
            // Database down or table missing: keep the defaults, but leave a trace in the log
            error_log('[BRANDING] ' . $e->getMessage());
        }

        $logo = systemBrandingLogoUrl($values['logo_path'], systemBrandingDefaultLogo());

        $brand = [
            'title'           => $values['short_name'],
            'name'            => $values['name'],
            'short_name'      => $values['short_name'],
            'version'         => $values['version'],
            'organization'    => $values['organization'],
            'subtitle'        => $values['organization'],
            'department_id'   => $values['department_id'],
            'support_email'   => $values['support_email'],
            'support_phone'   => $values['support_phone'],
            'logo_url'        => $logo,
            'agency_logo_url' => systemBrandingLogoUrl($values['agency_logo_path'], $logo),
            'copyright'       => $values['copyright'],
            'from_database'   => $fromDatabase,
        ];

        return $brand;
    }
}
