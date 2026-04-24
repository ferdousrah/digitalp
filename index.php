<?php

/**
 * Root index.php - Proxy to public/index.php
 *
 * This allows accessing the Laravel app at /digitalp/ instead of /digitalp/public/.
 * Static assets (CSS, JS, images) are served from public/ via .htaccess rewrite.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleRequest(Request::capture());
