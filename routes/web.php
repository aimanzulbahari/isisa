<?php
declare(strict_types=1);

/**
 * Web routes: short, clean URLs for the pages of the system.
 *
 *   http://localhost/isisa/Login   ->   app/auth/Login/Login.php
 *
 * How to add a page: add ONE entry below. The key is the URL path.
 * (URLs are not case sensitive: /Login and /login both work.)
 *
 *   'name'    Short name used to build links:  routeUrl('login')
 *   'file'    The PHP page, relative to the project folder.
 *             It must be inside "app/" (login pages) or "MyModules/" (system modules).
 *   'guard'   Who may open it (optional): guest | auth | role:ADMIN,SUPERADMIN | public
 *   'methods' Allowed HTTP methods (optional). Default: GET, POST, HEAD
 *   'handler' A function to run instead of a file (optional)
 *
 * The router that reads this list is core/helpers/routeHelpers.php,
 * and .htaccess sends every unknown URL to index.php.
 */
return [

    // "/" decides where to go: dashboard if logged in, login page if not
    '/' => [
        'name'    => 'home',
        'handler' => static function (): void {
            redirect(routeUrl(isLoggedIn() ? 'dashboard' : 'login'));
        },
    ],

    // Landing page (file in MyModules/Home): open to everyone, logged in or not
    '/Home' => [
        'name'  => 'home-page',
        'file'  => 'MyModules/Home/Home.php',
        'guard' => 'public',
    ],

    // --- Authentication pages (only for visitors who are NOT logged in) ---
    '/Login' => [
        'name'  => 'login',
        'file'  => 'app/auth/Login/Login.php',
        'guard' => 'guest',
    ],
    '/Register' => [
        'name'  => 'register',
        'file'  => 'app/auth/Register/Register.php',
        'guard' => 'guest',
    ],
    '/Forgot-Password' => [
        'name'  => 'forgot-password',
        'file'  => 'app/auth/Forgot-Password/Forgot-Password.php',
        'guard' => 'guest',
    ],

    '/Reset-Password' => [
        'name'  => 'reset-password',
        'file'  => 'app/auth/Reset-Password/Reset-Password.php',
        'guard' => 'guest',
    ],

    // --- Logout: no page, only an action. POST only (never a link), logged-in users only. ---
    // The "Log Keluar" button in src/layout/header.php posts a form here (with the CSRF token).
    '/Logout' => [
        'name'    => 'logout',
        'methods' => ['POST'],
        'guard'   => 'auth',
        'handler' => static function (): void {
            require_once APP_PATH . '/auth/Logout/LogoutController.php';
            handleLogout();
        },
    ],

    // --- Screen lock page (for logged-in users). Opened from "Kunci Skrin" in src/layout/header.php ---
    '/Lock-Screen' => [
        'name'  => 'lock-screen',
        'file'  => 'app/auth/Screen-Lock/Screen-Lock.php',
        'guard' => 'auth',
    ],

    // --- Guide: shows README.md as a page (files in MyModules/Guide) ---
    '/Guide' => [
        'name'  => 'guide',
        'file'  => 'MyModules/Guide/Guide.php',
        'guard' => 'auth',
    ],

    // --- Components-UI (UI templates, files in MyModules/Components-UI) ---
    // These pages need a login ('guard' => 'auth'). A visitor who is not logged in is sent to /Login.
    // The "file" path must use the real upper/lower case of the folders (Linux cares about it).
    '/Components-UI/Main-Components' => [
        'name'  => 'components-main',
        'file'  => 'MyModules/Components-UI/Main-Components/Main.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Basic' => [
        'name'  => 'components-basic',
        'file'  => 'MyModules/Components-UI/Basic/Basic.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Form' => [
        'name'  => 'components-form',
        'file'  => 'MyModules/Components-UI/Form/Form.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Tables' => [
        'name'  => 'components-tables',
        'file'  => 'MyModules/Components-UI/Tables/Tables.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Cards' => [
        'name'  => 'components-cards',
        'file'  => 'MyModules/Components-UI/Cards/Cards.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Charts' => [
        'name'  => 'components-charts',
        'file'  => 'MyModules/Components-UI/Charts/Charts.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Alerts' => [
        'name'  => 'components-alerts',
        'file'  => 'MyModules/Components-UI/Alerts/Alerts.php',
        'guard' => 'auth',
    ],
    '/Components-UI/Toastr' => [
        'name'  => 'components-toastr',
        'file'  => 'MyModules/Components-UI/Toastr/Toastr.php',
        'guard' => 'auth',
    ],

    // --- Dashboard sub pages (files in MyModules/Dashboard) ---
    '/Dashboard/Excecutive' => [
        'name'  => 'dashboard-executive',
        'file'  => 'MyModules/Dashboard/Excecutive/Excecutive.php',
        'guard' => 'auth',
    ],
    '/Dashboard/KPI-Analytic' => [
        'name'  => 'dashboard-kpi',
        'file'  => 'MyModules/Dashboard/KPI-Analytic/KPI-Analytic.php',
        'guard' => 'auth',
    ],
    '/Dashboard/Operation-Data' => [
        'name'  => 'dashboard-operation',
        'file'  => 'MyModules/Dashboard/Operation-Data/Operation-Data.php',
        'guard' => 'auth',
    ],
    '/Broadcast/Program-Activity' => [
        'name'  => 'broadcast-program',
        'file'  => 'MyModules/Broadcast/Program-Activity/Program-Activity.php',
        'guard' => 'auth',
    ],

    // --- Management (files in MyModules/Management) ---
    // Route names start with the menu group: wisma- / role- / users- (the sidebar uses this to open the group).
    '/Management/Wisma/Department' => [
        'name'  => 'wisma-department',
        'file'  => 'MyModules/Management/Wisma/Department/Department.php',
        'guard' => 'auth',
    ],
    '/Management/Wisma/Section' => [
        'name'  => 'wisma-section',
        'file'  => 'MyModules/Management/Wisma/Section/Section.php',
        'guard' => 'auth',
    ],
    '/Management/Wisma/Division' => [
        'name'  => 'wisma-division',
        'file'  => 'MyModules/Management/Wisma/Division/Division.php',
        'guard' => 'auth',
    ],
    '/Management/Wisma/Branch' => [
        'name'  => 'wisma-branch',
        'file'  => 'MyModules/Management/Wisma/Branch/Branch.php',
        'guard' => 'auth',
    ],
    '/Management/Role/Role' => [
        'name'  => 'role-role',
        'file'  => 'MyModules/Management/Role/Role/Role.php',
        'guard' => 'auth',
    ],
    '/Management/Role/Role-Type' => [
        'name'  => 'role-type',
        'file'  => 'MyModules/Management/Role/Role-Type/Role-Type.php',
        'guard' => 'auth',
    ],
    '/Management/Users/Users' => [
        'name'  => 'users-users',
        'file'  => 'MyModules/Management/Users/Users/Users.php',
        'guard' => 'auth',
    ],
    '/Management/Users/Employee' => [
        'name'  => 'users-employee',
        'file'  => 'MyModules/Management/Users/Employee/Employee.php',
        'guard' => 'auth',
    ],

    // --- Management > System (files in MyModules/Management/System) ---
    '/Management/System/Audit' => [
        'name'  => 'system-audit',
        'file'  => 'MyModules/Management/System/Audit/Audit.php',
        'guard' => 'auth',
    ],
    '/Management/System/Backup-Data' => [
        'name'  => 'system-backup',
        'file'  => 'MyModules/Management/System/Backup-Data/Backup-Data.php',
        'guard' => 'auth',
    ],
    '/Management/System/Info' => [
        'name'  => 'system-info',
        'file'  => 'MyModules/Management/System/Info/Info.php',
        'guard' => 'auth',
    ],
    '/Management/System/Maintenance' => [
        'name'  => 'system-maintenance',
        'file'  => 'MyModules/Management/System/Maintenance/Maintenance.php',
        'guard' => 'auth',
    ],

    // --- Announcement (file in MyModules/Broadcast/Announcement) ---
    '/Broadcast/Announcement' => [
        'name'  => 'announcement',
        'file'  => 'MyModules/Broadcast/Announcement/Announcement.php',
        'guard' => 'auth',
    ],

    // --- Notification (file in MyModules/Notifcation) ---
    '/Notification' => [
        'name'  => 'notification',
        'file'  => 'MyModules/Notifcation/Notification.php',
        'guard' => 'auth',
    ],

    // --- Accounts (file in MyModules/Accounts) ---
    '/Accounts' => [
        'name'  => 'accounts',
        'file'  => 'MyModules/Accounts/Accounts.php',
        'guard' => 'auth',
    ],

    // --- Helpdesk (files in MyModules/Helpdesk) ---
    '/Helpdesk/Helpdesk' => [
        'name'  => 'meja-dashboard',
        'file'  => 'MyModules/Helpdesk/Helpdesk/Helpdesk.php',
        'guard' => 'auth',
    ],
    '/Helpdesk/List' => [
        'name'  => 'meja-list',
        'file'  => 'MyModules/Helpdesk/List/List.php',
        'guard' => 'auth',
    ],
    // Download a ticket attachment (files are in storage/, which the browser cannot open directly).
    // Only the owner of the ticket, Admin and Superadmin may download it (checked in helpdeskDownload()).
    '/Helpdesk/Attachment' => [
        'name'    => 'meja-attachment',
        'guard'   => 'auth',
        'methods' => ['GET'],
        'handler' => static function (): void {
            require_once ROOT_PATH . '/MyModules/Helpdesk/List/ListController.php';
            helpdeskDownload();
        },
    ],

    // --- After login ---
    // "/Dashboard" has no page of its own: it sends the user to the first Dashboard page (Eksekutif).
    '/Dashboard' => [
        'name'    => 'dashboard',
        'handler' => static function (): void {
            redirect(routeUrl('dashboard-executive'));
        },
        'guard'   => 'auth',
    ],

];
