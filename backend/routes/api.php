<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\QuoteRequestController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ErpOrderController;
use App\Http\Controllers\Api\ErpCustomerController;
use App\Http\Controllers\Api\ErpSupplierController;
use App\Http\Controllers\Api\ErpCashbookController;
use App\Http\Controllers\Api\ErpFinanceController;
use App\Http\Controllers\Api\GoogleIndexingController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - In Ấn Nhật Nguyên
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Authentication (Public login)
    Route::post('/auth/login', [AuthController::class, 'login']);

    // 2. Public Catalog & Quotes & Settings & Banners & Posts
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/banners', [BannerController::class, 'index']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{slug}', [PostController::class, 'show']);
    Route::post('/quotes', [QuoteRequestController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/settings', [SettingController::class, 'index']);

    // 3. Admin Protected Routes (Bearer Token via Sanctum & Granular RBAC Permissions)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // Dashboard statistics (Accessible to any active staff, views adapt to permissions)
        Route::get('/admin/dashboard-stats', [DashboardController::class, 'stats']);

        // Quote Requests Management
        Route::get('/admin/quotes', [QuoteRequestController::class, 'index'])->middleware('permission:quotes.view');
        Route::get('/admin/quotes/{id}', [QuoteRequestController::class, 'show'])->middleware('permission:quotes.view');
        Route::put('/admin/quotes/{id}', [QuoteRequestController::class, 'update'])->middleware('permission:quotes.process');
        Route::delete('/admin/quotes/{id}', [QuoteRequestController::class, 'destroy'])->middleware('permission:quotes.delete');

        // Categories Management
        Route::post('/admin/categories', [CategoryController::class, 'store'])->middleware('permission:categories.manage');
        Route::put('/admin/categories/{id}', [CategoryController::class, 'update'])->middleware('permission:categories.manage');
        Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy'])->middleware('permission:categories.manage');

        // Products Management
        Route::post('/admin/products', [ProductController::class, 'store'])->middleware('permission:products.manage');
        Route::put('/admin/products/{id}', [ProductController::class, 'update'])->middleware('permission:products.manage');
        Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->middleware('permission:products.manage');

        // Banners Management
        Route::get('/admin/banners', [BannerController::class, 'index'])->middleware('permission:banners.manage');
        Route::post('/admin/banners', [BannerController::class, 'store'])->middleware('permission:banners.manage');
        Route::get('/admin/banners/{id}', [BannerController::class, 'show'])->middleware('permission:banners.manage');
        Route::put('/admin/banners/{id}', [BannerController::class, 'update'])->middleware('permission:banners.manage');
        Route::delete('/admin/banners/{id}', [BannerController::class, 'destroy'])->middleware('permission:banners.manage');
        Route::post('/admin/banners/{id}/toggle-active', [BannerController::class, 'toggleActive'])->middleware('permission:banners.manage');
        Route::post('/admin/banners/reorder', [BannerController::class, 'reorder'])->middleware('permission:banners.manage');

        // Posts & Articles Management (Sanitized & Isolated for Writers)
        Route::get('/admin/posts', [PostController::class, 'index'])->middleware('permission:posts.manage');
        Route::post('/admin/posts', [PostController::class, 'store'])->middleware('permission:posts.manage');
        Route::get('/admin/posts/{id}', [PostController::class, 'show'])->middleware('permission:posts.manage');
        Route::put('/admin/posts/{id}', [PostController::class, 'update'])->middleware('permission:posts.manage');
        Route::delete('/admin/posts/{id}', [PostController::class, 'destroy'])->middleware('permission:posts.manage');
        Route::post('/admin/posts/{id}/toggle-publish', [PostController::class, 'togglePublish'])->middleware('permission:posts.manage');

        // Site Settings Management
        Route::post('/admin/settings', [SettingController::class, 'update'])->middleware('permission:settings.manage');

        // Google Indexing API
        Route::get('/admin/google-indexing/config', [GoogleIndexingController::class, 'getConfig'])->middleware('permission:seo_indexing.manage');
        Route::post('/admin/google-indexing/config', [GoogleIndexingController::class, 'saveConfig'])->middleware('permission:seo_indexing.manage');
        Route::post('/admin/google-indexing/test', [GoogleIndexingController::class, 'testConnection'])->middleware('permission:seo_indexing.manage');
        Route::post('/admin/google-indexing/publish', [GoogleIndexingController::class, 'publish'])->middleware('permission:seo_indexing.manage');
        Route::get('/admin/google-indexing/metadata', [GoogleIndexingController::class, 'getMetadata'])->middleware('permission:seo_indexing.manage');

        // Media Library Management
        Route::get('/admin/media', [MediaController::class, 'index'])->middleware('permission:media.manage');
        Route::post('/admin/media/upload', [MediaController::class, 'upload'])->middleware('permission:media.manage');
        Route::delete('/admin/media', [MediaController::class, 'destroy'])->middleware('permission:media.manage');
        Route::post('/admin/media/folders', [MediaController::class, 'createFolder'])->middleware('permission:media.manage');
        Route::delete('/admin/media/folders', [MediaController::class, 'deleteFolder'])->middleware('permission:media.manage');

        // ERP - Orders & Production
        Route::get('/admin/erp/orders', [ErpOrderController::class, 'index'])->middleware('permission:orders.view');
        Route::get('/admin/erp/orders/next-code', [ErpOrderController::class, 'generateNextCode'])->middleware('permission:orders.view');
        Route::post('/admin/erp/orders', [ErpOrderController::class, 'store'])->middleware('permission:orders.create');
        Route::get('/admin/erp/orders/{id}', [ErpOrderController::class, 'show'])->middleware('permission:orders.view');
        Route::put('/admin/erp/orders/{id}', [ErpOrderController::class, 'update'])->middleware('permission:orders.edit');
        Route::delete('/admin/erp/orders/{id}', [ErpOrderController::class, 'destroy'])->middleware('permission:orders.delete');
        Route::post('/admin/erp/orders/{id}/stage', [ErpOrderController::class, 'updateStage'])->middleware('permission:orders.stage');
        Route::post('/admin/erp/orders/{id}/payments', [ErpOrderController::class, 'addPayment'])->middleware('permission:orders.payment');
        Route::post('/admin/erp/orders/{id}/request-delete', [ErpOrderController::class, 'requestDelete'])->middleware('permission:orders.delete');
        Route::get('/admin/erp/orders/{id}/vietqr', [ErpOrderController::class, 'vietQR'])->middleware('permission:orders.view');

        // ERP - Customers & CRM
        Route::get('/admin/erp/customers', [ErpCustomerController::class, 'index'])->middleware('permission:clients.view');
        Route::get('/admin/erp/customers/search', [ErpCustomerController::class, 'search'])->middleware('permission:clients.view');
        Route::get('/admin/erp/customers/daily-care', [ErpCustomerController::class, 'dailyCare'])->middleware('permission:clients.care');
        Route::post('/admin/erp/customers', [ErpCustomerController::class, 'store'])->middleware('permission:clients.manage');
        Route::get('/admin/erp/customers/{id}', [ErpCustomerController::class, 'show'])->middleware('permission:clients.view');
        Route::put('/admin/erp/customers/{id}', [ErpCustomerController::class, 'update'])->middleware('permission:clients.manage');
        Route::delete('/admin/erp/customers/{id}', [ErpCustomerController::class, 'destroy'])->middleware('permission:clients.delete');
        Route::post('/admin/erp/customers/import', [ErpCustomerController::class, 'import'])->middleware('permission:clients.manage');

        // ERP - Suppliers & Debt
        Route::get('/admin/erp/suppliers', [ErpSupplierController::class, 'index'])->middleware('permission:suppliers.view');
        Route::post('/admin/erp/suppliers', [ErpSupplierController::class, 'store'])->middleware('permission:suppliers.manage');
        Route::get('/admin/erp/suppliers/{id}', [ErpSupplierController::class, 'show'])->middleware('permission:suppliers.view');
        Route::put('/admin/erp/suppliers/{id}', [ErpSupplierController::class, 'update'])->middleware('permission:suppliers.manage');
        Route::delete('/admin/erp/suppliers/{id}', [ErpSupplierController::class, 'destroy'])->middleware('permission:suppliers.manage');
        Route::post('/admin/erp/suppliers/{id}/payments', [ErpSupplierController::class, 'recordPayment'])->middleware('permission:suppliers.manage');

        // ERP - Cashbook
        Route::get('/admin/erp/cashbook', [ErpCashbookController::class, 'index'])->middleware('permission:cashbook.view');
        Route::post('/admin/erp/cashbook', [ErpCashbookController::class, 'store'])->middleware('permission:cashbook.create');
        Route::get('/admin/erp/cashbook/statistics', [ErpCashbookController::class, 'statistics'])->middleware('permission:cashbook.view');
        Route::get('/admin/erp/cashbook/{id}', [ErpCashbookController::class, 'show'])->middleware('permission:cashbook.view');
        Route::put('/admin/erp/cashbook/{id}', [ErpCashbookController::class, 'update'])->middleware('permission:cashbook.manage');
        Route::delete('/admin/erp/cashbook/{id}', [ErpCashbookController::class, 'destroy'])->middleware('permission:cashbook.manage');

        // ERP - Finance & Profit Sharing (High Security)
        Route::get('/admin/erp/finance/dashboard', [ErpFinanceController::class, 'dashboardOverview'])->middleware('permission:finance.dashboard');
        Route::get('/admin/erp/finance/profit-sharing', [ErpFinanceController::class, 'profitSharing'])->middleware('permission:finance.profit_sharing');

        // Users & Staff Management
        Route::get('/admin/users', [AdminUserController::class, 'index'])->middleware('permission:users.view');
        Route::post('/admin/users', [AdminUserController::class, 'store'])->middleware('permission:users.manage');
        Route::get('/admin/users/{id}', [AdminUserController::class, 'show'])->middleware('permission:users.view');
        Route::put('/admin/users/{id}', [AdminUserController::class, 'update'])->middleware('permission:users.manage');
        Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->middleware('permission:users.manage');
        Route::post('/admin/users/{id}/toggle-active', [AdminUserController::class, 'toggleActive'])->middleware('permission:users.manage');

        // Roles & Permissions Management
        Route::get('/admin/roles', [AdminRoleController::class, 'index'])->middleware('permission:users.view');
        Route::post('/admin/roles', [AdminRoleController::class, 'store'])->middleware('permission:users.manage');
        Route::put('/admin/roles/{id}', [AdminRoleController::class, 'update'])->middleware('permission:users.manage');
        Route::delete('/admin/roles/{id}', [AdminRoleController::class, 'destroy'])->middleware('permission:users.manage');
        Route::get('/admin/permissions', [AdminRoleController::class, 'permissions'])->middleware('permission:users.view');
        Route::post('/admin/roles/{id}/permissions', [AdminRoleController::class, 'updatePermissions'])->middleware('permission:users.manage');
    });
});
