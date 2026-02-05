<?php
// public/index.php

use App\Core\Request;

try {
    [$router] = require __DIR__ . '/../bootstrap/app.php';
    $router->dispatch(new Request());
} catch (Throwable $e) {
    $debug = defined('APP_DEBUG') ? APP_DEBUG : true;

    if ($debug) {
        echo '<h1>Error</h1>';
        echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p><strong>File:</strong> ' . $e->getFile() . '</p>';
        echo '<p><strong>Line:</strong> ' . $e->getLine() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>Sorry, something went wrong.</p>';
    }

    // Log if possible
    if (defined('LOG_PATH') && is_dir(LOG_PATH)) {
        $logFile = LOG_PATH . '/error-' . date('Y-m-d') . '.log';
        $message = date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . PHP_EOL;
        $message .= 'Stack trace: ' . $e->getTraceAsString() . PHP_EOL;
        @file_put_contents($logFile, $message, FILE_APPEND);
    }
}
