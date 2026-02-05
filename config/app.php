<?php
// config/app.php
return [
    'name' => APP_NAME,
    'env' => APP_ENV,
    'debug' => APP_DEBUG,
    'url' => SITE_URL,
    'timezone' => 'Asia/Ho_Chi_Minh',
    
    'providers' => [],
    
    'aliases' => [],
    
    'database' => require __DIR__ . '/database.php',
    
    'session' => [
        'driver' => 'file',
        'lifetime' => SESSION_LIFETIME,
        'files' => STORAGE_PATH . '/sessions',
        'cookie' => SESSION_NAME,
    ],
    
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.gmail.com',
        'port' => 587,
        'encryption' => 'tls',
        'username' => '',
        'password' => '',
        'from' => [
            'address' => MAIL_FROM_ADDRESS,
            'name' => MAIL_FROM_NAME,
        ],
    ],
];
?>