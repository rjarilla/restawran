<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Redirect any /public URL to the app root so the public website is shown.
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('#^/public(/|$)#', $requestUri)) {
    $redirectTo = preg_replace('#^/public#', '', $requestUri);
    if ($redirectTo === '') {
        $redirectTo = '/';
    }
    header('Location: ' . $redirectTo, true, 301);
    exit;
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
