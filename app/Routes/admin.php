<?php
use App\Middleware\AdminMiddleware;

// Admin routes group with admin middleware
$router->group(['prefix' => '/admin', 'middleware' => 'admin'], function($router) {
    
    // Dashboard
    $router->get('/', [App\Controllers\Admin\AdminController::class, 'dashboard']);
    
    // Products
    $router->get('/products', [App\Controllers\Admin\ProductController::class, 'index']);
    $router->get('/products/create', [App\Controllers\Admin\ProductController::class, 'create']);
    $router->post('/products', [App\Controllers\Admin\ProductController::class, 'store']);
    $router->get('/products/{id}/edit', [App\Controllers\Admin\ProductController::class, 'edit']);
    $router->post('/products/{id}', [App\Controllers\Admin\ProductController::class, 'update']);
    $router->get('/products/{id}/delete', [App\Controllers\Admin\ProductController::class, 'delete']);
    $router->get('/products/export', [App\Controllers\Admin\ProductController::class, 'export']);
    $router->post('/products/import', [App\Controllers\Admin\ProductController::class, 'import']);
    
    // Categories
    $router->get('/categories', [App\Controllers\Admin\CategoryController::class, 'index']);
    $router->get('/categories/create', [App\Controllers\Admin\CategoryController::class, 'create']);
    $router->post('/categories', [App\Controllers\Admin\CategoryController::class, 'store']);
    $router->get('/categories/{id}/edit', [App\Controllers\Admin\CategoryController::class, 'edit']);
    $router->post('/categories/{id}', [App\Controllers\Admin\CategoryController::class, 'update']);
    $router->get('/categories/{id}/delete', [App\Controllers\Admin\CategoryController::class, 'delete']);
    
    // Brands
    $router->get('/brands', [App\Controllers\Admin\BrandController::class, 'index']);
    $router->get('/brands/create', [App\Controllers\Admin\BrandController::class, 'create']);
    $router->post('/brands', [App\Controllers\Admin\BrandController::class, 'store']);
    $router->get('/brands/{id}/edit', [App\Controllers\Admin\BrandController::class, 'edit']);
    $router->post('/brands/{id}', [App\Controllers\Admin\BrandController::class, 'update']);
    $router->get('/brands/{id}/delete', [App\Controllers\Admin\BrandController::class, 'delete']);
    
    // Orders
    $router->get('/orders', [App\Controllers\Admin\AdminController::class, 'orders']);
    $router->get('/orders/{id}', [App\Controllers\Admin\AdminController::class, 'viewOrder']);
    $router->post('/orders/{id}/status', [App\Controllers\Admin\AdminController::class, 'updateOrderStatus']);
    $router->get('/orders/{id}/invoice', [App\Controllers\Admin\AdminController::class, 'generateInvoice']);
    $router->get('/orders/export', [App\Controllers\Admin\AdminController::class, 'exportOrders']);
    
    // Users
    $router->get('/users', [App\Controllers\Admin\AdminController::class, 'users']);
    $router->get('/users/{id}', [App\Controllers\Admin\AdminController::class, 'viewUser']);
    $router->post('/users/{id}', [App\Controllers\Admin\AdminController::class, 'updateUser']);
    $router->get('/users/{id}/delete', [App\Controllers\Admin\AdminController::class, 'deleteUser']);
    $router->get('/users/{id}/orders', [App\Controllers\Admin\AdminController::class, 'userOrders']);
    
    // Settings
    $router->get('/settings', [App\Controllers\Admin\AdminController::class, 'settings']);
    $router->post('/settings', [App\Controllers\Admin\AdminController::class, 'updateSettings']);
    $router->get('/settings/shipping', [App\Controllers\Admin\AdminController::class, 'shippingSettings']);
    $router->post('/settings/shipping', [App\Controllers\Admin\AdminController::class, 'updateShippingSettings']);
    $router->get('/settings/payment', [App\Controllers\Admin\AdminController::class, 'paymentSettings']);
    $router->post('/settings/payment', [App\Controllers\Admin\AdminController::class, 'updatePaymentSettings']);
    
    // Reports
    $router->get('/reports/sales', [App\Controllers\Admin\AdminController::class, 'salesReport']);
    $router->get('/reports/products', [App\Controllers\Admin\AdminController::class, 'productsReport']);
    $router->get('/reports/customers', [App\Controllers\Admin\AdminController::class, 'customersReport']);
    $router->post('/reports/generate', [App\Controllers\Admin\AdminController::class, 'generateReport']);
    
    // Media
    $router->get('/media', [App\Controllers\Admin\AdminController::class, 'media']);
    $router->post('/media/upload', [App\Controllers\Admin\AdminController::class, 'uploadMedia']);
    $router->get('/media/{id}/delete', [App\Controllers\Admin\AdminController::class, 'deleteMedia']);
});