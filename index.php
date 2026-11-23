<?php
declare(strict_types=1);

/**
 * Front controller: every URL that is not a real file ends up here
 * (see the rewrite rule in .htaccess). It starts the system and runs the
 * page that matches the URL, using the list in routes/web.php.
 */
require_once __DIR__ . '/core/config/bootstrap.php';

dispatchRoutes();
