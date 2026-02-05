<?php
// Application
define('APP_NAME', 'E-Commerce-k25VTC');
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // production, development, testing
define('APP_DEBUG', (getenv('APP_DEBUG') ?? '1') === '1');

// Backward-compatible alias used across controllers/views
// (PHP 8+ treats undefined constants as fatal)
define('SITE_NAME', APP_NAME);

// URLs
// Prefer an explicit APP_URL env var; otherwise infer from the current request.
$__appUrl = getenv('APP_URL');
if (!$__appUrl) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $scriptDir = rtrim($scriptDir, '/');
    $__appUrl = $scheme . '://' . $host . ($scriptDir === '' ? '' : $scriptDir);
}
define('SITE_URL', rtrim($__appUrl, '/'));
define('ADMIN_URL', SITE_URL . '/admin');
define('API_URL', SITE_URL . '/api');

// Paths
// This file can be included from public/index.php, which may also define ROOT_PATH.
// Guard to avoid "already defined" fatals.
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) define('APP_PATH', ROOT_PATH . '/app');
if (!defined('PUBLIC_PATH')) define('PUBLIC_PATH', ROOT_PATH . '/public');
if (!defined('VIEW_PATH')) define('VIEW_PATH', ROOT_PATH . '/views');
if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
if (!defined('STORAGE_PATH')) define('STORAGE_PATH', ROOT_PATH . '/storage');
if (!defined('LOG_PATH')) define('LOG_PATH', STORAGE_PATH . '/logs');
if (!defined('CACHE_PATH')) define('CACHE_PATH', STORAGE_PATH . '/cache');

// Database (can be overridden by .env)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'ecommerce_oop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATION', 'utf8mb4_unicode_ci');

// Tables (dùng tên bảng từ database của bạn)
define('TABLE_USERS', 'users');
define('TABLE_PRODUCTS', 'products');
define('TABLE_CATEGORIES', 'categories');
define('TABLE_BRANDS', 'brands');
define('TABLE_CART_ITEMS', 'cart_items');
define('TABLE_ORDERS', 'orders');
define('TABLE_ORDER_ITEMS', 'order_items');

// Security
define('ENCRYPTION_KEY', 'ecommerce-secure-key-2024');
define('JWT_SECRET', 'jwt-secret-key-2024');
define('CSRF_TOKEN_NAME', '_token');
define('SESSION_NAME', 'ecommerce_session');
define('SESSION_LIFETIME', 7200); // 2 hours

// Pagination
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 25);

// Upload
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Email
define('MAIL_FROM_NAME', APP_NAME);
define('MAIL_FROM_ADDRESS', 'noreply@localhost.com');

// Payment
define('CURRENCY', 'VND');
define('CURRENCY_SYMBOL', '₫');
define('TAX_RATE', 0.1); // 10%
define('SHIPPING_FEE', 30000); // 30,000 VND

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting based on environment
if (APP_ENV === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Create necessary directories if they don't exist
if (!is_dir(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}
if (!is_dir(CACHE_PATH)) {
    mkdir(CACHE_PATH, 0755, true);
}

// Sessions storage (used by config/app.php)
$__sessionPath = STORAGE_PATH . '/sessions';
if (!is_dir($__sessionPath)) {
    mkdir($__sessionPath, 0755, true);
}
?>