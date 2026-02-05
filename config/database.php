<?php
// config/database.php
return [
    'host' => (getenv('DB_HOST') ?: DB_HOST),
    'port' => DB_PORT,
    'database' => (getenv('DB_NAME') ?: DB_NAME),
    'username' => (getenv('DB_USER') ?: DB_USER),
    'password' => (getenv('DB_PASS') ?: DB_PASS),
    'charset' => DB_CHARSET,
    'collation' => DB_COLLATION,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
?>