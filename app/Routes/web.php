<?php
use App\Middleware\AuthMiddleware;

// Home routes
$router->get('/', [App\Controllers\HomeController::class, 'index']);
$router->get('/products', [App\Controllers\HomeController::class, 'products']);
$router->get('/product/{id}', [App\Controllers\HomeController::class, 'productDetail']);
$router->get('/search', [App\Controllers\HomeController::class, 'search']);
$router->get('/category/{id}', [App\Controllers\HomeController::class, 'category']);
$router->get('/brand/{id}', [App\Controllers\HomeController::class, 'brand']);

// Newsletter
$router->post('/newsletter/subscribe', [App\Controllers\HomeController::class, 'subscribeNewsletter']);

// User routes
$router->get('/register', [App\Controllers\UserController::class, 'register']);
$router->post('/register', [App\Controllers\UserController::class, 'register']);
$router->get('/login', [App\Controllers\UserController::class, 'login']);
$router->post('/login', [App\Controllers\UserController::class, 'login']);
$router->get('/verify-otp', [App\Controllers\UserController::class, 'verifyOtp']);
$router->post('/verify-otp', [App\Controllers\UserController::class, 'verifyOtp']);
$router->get('/logout', [App\Controllers\UserController::class, 'logout']);
$router->get('/profile', [App\Controllers\UserController::class, 'profile']);
$router->post('/profile', [App\Controllers\UserController::class, 'updateProfile']);
$router->get('/orders', [App\Controllers\UserController::class, 'orders']);
$router->get('/orders/{id}', [App\Controllers\UserController::class, 'viewOrder']);
$router->get('/forgot-password', [App\Controllers\UserController::class, 'forgotPassword']);
$router->post('/forgot-password', [App\Controllers\UserController::class, 'forgotPassword']);
$router->get('/reset-password/{token}', [App\Controllers\UserController::class, 'resetPassword']);
$router->post('/reset-password/{token}', [App\Controllers\UserController::class, 'resetPassword']);

// Cart routes
$router->get('/cart', [App\Controllers\CartController::class, 'index']);
$router->post('/cart/add/{id}', [App\Controllers\CartController::class, 'add']);
$router->post('/cart/update', [App\Controllers\CartController::class, 'update']);
$router->get('/cart/remove/{id}', [App\Controllers\CartController::class, 'remove']);
$router->get('/cart/clear', [App\Controllers\CartController::class, 'clear']);

// Checkout routes
$router->get('/checkout', [App\Controllers\CartController::class, 'checkout']);
$router->post('/checkout', [App\Controllers\CartController::class, 'processCheckout']);
$router->get('/checkout/success', [App\Controllers\CartController::class, 'checkoutSuccess']);
$router->get('/checkout/cancel', [App\Controllers\CartController::class, 'checkoutCancel']);

// Protected routes (require authentication)
$router->group(['middleware' => 'auth'], function($router) {
    $router->get('/wishlist', [App\Controllers\UserController::class, 'wishlist']);
    $router->post('/wishlist/add/{id}', [App\Controllers\UserController::class, 'addToWishlist']);
    $router->get('/wishlist/remove/{id}', [App\Controllers\UserController::class, 'removeFromWishlist']);
    
    $router->get('/addresses', [App\Controllers\UserController::class, 'addresses']);
    $router->post('/addresses', [App\Controllers\UserController::class, 'addAddress']);
    $router->post('/addresses/{id}', [App\Controllers\UserController::class, 'updateAddress']);
    $router->get('/addresses/{id}/delete', [App\Controllers\UserController::class, 'deleteAddress']);
    
    $router->get('/change-password', [App\Controllers\UserController::class, 'changePassword']);
    $router->post('/change-password', [App\Controllers\UserController::class, 'changePassword']);
});