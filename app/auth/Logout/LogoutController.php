<?php
declare(strict_types=1);

/**
 * LogoutController: the logic of logging out.
 *
 * There is no logout page. The "Log Keluar" button in src/layout/header.php sends a
 * POST form to /Logout (see routes/web.php), and the route calls handleLogout().
 *
 * Safety:
 *   - POST only (the route refuses GET), so another website cannot log a user out
 *     with a simple link or image.
 *   - The CSRF token is already checked for every POST by core/includes/csrf.php.
 *   - Only logged-in users reach this (route guard 'auth').
 *   - This file only DEFINES a function. Opening the file directly in the browser does nothing.
 */

require_once __DIR__ . '/LogoutModel.php';

if (!function_exists('handleLogout')) {
    /**
     * Log the current user out and go to the login page. Never returns.
     *
     * Steps:
     *   1. Write the logout time in login_logs
     *   2. Write the LOGOUT event in audits (while we still know who the user is)
     *   3. Wipe the session data and give the browser a NEW session ID
     *   4. Redirect to /Login with a message that is shown once (Post/Redirect/Get)
     */
    function handleLogout(): never
    {
        $userId = currentUserId();

        if ($userId !== null) {
            LogoutModel::closeLoginLog((string) session_id());
            audit('LOGOUT', 'users', $userId);
        }

        // Remove everything the user had in the session (user_id, CSRF token, intended page...)
        $_SESSION = [];

        // New session ID; "true" also deletes the old session row from the database.
        // This is safer than reusing the same ID after logout.
        session_regenerate_id(true);

        // Message for the login page (read once by LoginController::handleLogin)
        $_SESSION['login_flash'] = [
            'type'      => 'success',
            'message'   => 'Anda telah berjaya log keluar.',
            'old_no_kp' => '',
        ];

        redirect(routeUrl('login'), 303); // 303 = "See Other": answer a POST with a GET
    }
}
