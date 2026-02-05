<?php
// bootstrap/app.php

// 1) Paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// 2) Load env (optional)
require_once ROOT_PATH . '/bootstrap/env.php';
loadEnv(ROOT_PATH);

// 3) Load config constants (expects env available)
require_once ROOT_PATH . '/config/constants.php';

// 4) Composer autoload (preferred) – fallback to legacy autoload if vendor missing
$vendorAutoload = ROOT_PATH . '/vendor/autoload.php';
if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
} else {
    // Legacy PSR-4 autoload fallback (keeps project runnable even without composer install)
    spl_autoload_register(function ($class) {
        $prefix = 'App\\';
        $base_dir = ROOT_PATH . '/app/';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    });

    // If composer isn't installed, helpers won't be auto-loaded
    require_once ROOT_PATH . '/app/Utils/Helpers.php';
}

// 5) Start session (after constants so SESSION_NAME is defined)
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// 6) Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

use App\Core\Auth;
use App\Core\Container;
use App\Core\Database;
use App\Core\Response;
use App\Core\Router;

// 7) DI container
$container = new Container();

$container->singleton('session', function() {
    return new \App\Core\Session();
});

$container->singleton('db', function() {
    $config = require ROOT_PATH . '/config/database.php';
    return new Database(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );
});

$container->singleton(Response::class, function() {
    return new Response();
});

$container->singleton(Auth::class, function($c) {
    return new Auth($c->get('session'));
});

// 8) Router + routes
$router = new Router($container);
require_once ROOT_PATH . '/app/Routes/web.php';
require_once ROOT_PATH . '/app/Routes/admin.php';

return [$router, $container];
