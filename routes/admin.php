<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\WooCommerceController;
use App\Models\Brand;
use App\Models\ProductAttribute;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::bind('shipping', fn ($value) => ShippingMethod::findOrFail($value));
Route::bind('customer', fn ($value) => User::findOrFail($value));
Route::bind('attribute', fn ($value) => ProductAttribute::findOrFail($value));
Route::bind('brand', fn ($value) => Brand::findOrFail($value));
Route::bind('user', fn ($value) => User::findOrFail($value));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');
    Route::get('/search', [\App\Http\Controllers\Admin\GlobalSearchController::class, 'search'])->name('search');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Products & Variations
    Route::get('/products-bulk/create', [ProductController::class, 'bulkCreate'])->middleware('permission:products.bulk_manage')->name('products.bulkCreate');
    Route::post('/products-bulk/store', [ProductController::class, 'bulkStore'])->middleware('permission:products.bulk_manage')->name('products.bulkStore');
    Route::get('/products-import', [ProductController::class, 'importForm'])->middleware('permission:products.import')->name('products.import');
    Route::post('/products-import', [ProductController::class, 'importStore'])->middleware('permission:products.import')->name('products.importStore');
    Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->middleware('permission:products.edit')->name('products.toggleStatus');
    Route::patch('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->middleware('permission:products.edit')->name('products.toggleFeatured');
    
    Route::get('/products/{product}/variations', [\App\Http\Controllers\Admin\ProductVariationController::class, 'index'])->middleware('permission:products.view')->name('products.variations.index');
    Route::post('/products/{product}/variations', [\App\Http\Controllers\Admin\ProductVariationController::class, 'store'])->middleware('permission:products.edit')->name('products.variations.store');
    Route::put('/products/{product}/variations/{variation}', [\App\Http\Controllers\Admin\ProductVariationController::class, 'update'])->middleware('permission:products.edit')->name('products.variations.update');
    Route::delete('/products/{product}/variations/{variation}', [\App\Http\Controllers\Admin\ProductVariationController::class, 'destroy'])->middleware('permission:products.edit')->name('products.variations.destroy');

    Route::resource('products', ProductController::class)->middleware('permission:products.view');

    // Categories
    Route::resource('categories', CategoryController::class)->except(['show'])->middleware('permission:categories.view');

    // Brands
    Route::resource('brands', BrandController::class)->except(['show'])->middleware('permission:brands.view');

    // Attributes
    Route::resource('attributes', AttributeController::class)->except(['show'])->middleware('permission:attributes.view');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('inventory.index');
    Route::put('/inventory/{product}', [InventoryController::class, 'update'])->middleware('permission:inventory.adjust')->name('inventory.update');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->middleware('permission:orders.view')->name('orders.index');
    Route::post('/orders/bulk-action', [OrderController::class, 'bulkAction'])->middleware('permission:orders.edit')->name('orders.bulkAction');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->middleware('permission:orders.view')->name('orders.show');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->middleware('permission:orders.edit')->name('orders.update');
    Route::post('/orders/{order}/verify-payment', [OrderController::class, 'verifyPaymentManually'])->middleware('permission:orders.edit')->name('orders.verifyPayment');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->middleware('permission:customers.view')->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->middleware('permission:customers.view')->name('customers.show');

    // Coupons, Taxes, Shipping
    Route::resource('coupons', CouponController::class)->except(['show'])->middleware('permission:coupons.view');
    Route::resource('taxes', TaxController::class)->except(['show'])->middleware('permission:taxes.view');
    Route::resource('shipping', ShippingController::class)->except(['show'])->middleware('permission:shipping.view');

    // Payments & Invoices
    Route::get('/payments', [PaymentController::class, 'index'])->middleware('permission:payments.view')->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->middleware('permission:payments.view')->name('payments.show');

    Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('permission:invoices.view')->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->middleware('permission:invoices.view')->name('invoices.show');

    // Inquiries
    Route::get('/inquiries', [InquiryController::class, 'index'])->middleware('permission:inquiries.view')->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])->middleware('permission:inquiries.view')->name('inquiries.show');
    Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])->middleware('permission:inquiries.manage')->name('inquiries.update');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('reports.index');
    Route::get('/reports/profit', [ReportController::class, 'profit'])->middleware('permission:reports.view')->name('reports.profit');
    Route::get('/reports/gst', [ReportController::class, 'gst'])->middleware('permission:reports.view')->name('reports.gst');
    Route::get('/reports/export/{module}', [ReportController::class, 'export'])->middleware('permission:reports.view')->name('reports.export');

    // CMS (Banners, Pages)
    Route::resource('banners', BannerController::class)->except(['show'])->middleware('permission:cms.view');
    Route::resource('pages', PageController::class)->except(['show'])->middleware('permission:cms.view');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->middleware('permission:settings.view')->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->middleware('permission:settings.manage')->name('settings.update');

    // Admin Users Management (Staff)
    Route::get('/users', [AdminUserController::class, 'index'])->middleware('permission:users.view')->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->middleware('permission:users.manage')->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->middleware('permission:users.manage')->name('users.edit');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
    Route::patch('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->middleware('permission:users.manage')->name('users.toggleStatus');

    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->middleware('permission:activity_logs.view')->name('activity-logs.index');

    // WooCommerce Integration & Sync Modules
    Route::get('/woocommerce', [WooCommerceController::class, 'index'])->middleware('permission:woocommerce.view')->name('woocommerce.index');
    Route::post('/woocommerce/test-connection', [WooCommerceController::class, 'testConnection'])->middleware('permission:woocommerce.manage')->name('woocommerce.testConnection');

    Route::get('/woocommerce/products', [\App\Http\Controllers\Admin\WooCommerceProductSyncController::class, 'index'])->middleware('permission:woocommerce.view')->name('woocommerce.products.index');
    Route::post('/woocommerce/products/{product}/sync', [\App\Http\Controllers\Admin\WooCommerceProductSyncController::class, 'syncSingle'])->middleware('permission:woocommerce.manage')->name('woocommerce.products.syncSingle');
    Route::post('/woocommerce/products/bulk-sync', [\App\Http\Controllers\Admin\WooCommerceProductSyncController::class, 'bulkSync'])->middleware('permission:woocommerce.manage')->name('woocommerce.products.bulkSync');
    Route::get('/woocommerce/products/{product}/error', [\App\Http\Controllers\Admin\WooCommerceProductSyncController::class, 'showError'])->middleware('permission:woocommerce.view')->name('woocommerce.products.showError');

    Route::get('/woocommerce/sync-logs', [\App\Http\Controllers\Admin\WooCommerceSyncLogController::class, 'index'])->middleware('permission:woocommerce.view')->name('woocommerce.sync-logs.index');
    Route::get('/woocommerce/sync-logs/{log}', [\App\Http\Controllers\Admin\WooCommerceSyncLogController::class, 'show'])->middleware('permission:woocommerce.view')->name('woocommerce.sync-logs.show');
    Route::post('/woocommerce/sync-logs/{log}/retry', [\App\Http\Controllers\Admin\WooCommerceSyncLogController::class, 'retry'])->middleware('permission:woocommerce.manage')->name('woocommerce.sync-logs.retry');

    Route::get('/woocommerce/conflicts', [\App\Http\Controllers\Admin\WooCommerceSyncConflictController::class, 'index'])->middleware('permission:woocommerce.view')->name('woocommerce.conflicts.index');
    Route::post('/woocommerce/conflicts/{conflict}/resolve', [\App\Http\Controllers\Admin\WooCommerceSyncConflictController::class, 'resolve'])->middleware('permission:woocommerce.manage')->name('woocommerce.conflicts.resolve');

    // Sales Returns
    Route::get('/returns', [\App\Http\Controllers\Admin\ReturnController::class, 'index'])->middleware('permission:orders.view')->name('returns.index');
    Route::get('/returns/{return}', [\App\Http\Controllers\Admin\ReturnController::class, 'show'])->middleware('permission:orders.view')->name('returns.show');
    Route::post('/returns/{return}/update-status', [\App\Http\Controllers\Admin\ReturnController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('returns.updateStatus');

    // Sales RTO
    Route::get('/rto', [\App\Http\Controllers\Admin\RtoController::class, 'index'])->middleware('permission:orders.view')->name('rto.index');
    Route::get('/rto/create', [\App\Http\Controllers\Admin\RtoController::class, 'create'])->middleware('permission:orders.edit')->name('rto.create');
    Route::post('/rto', [\App\Http\Controllers\Admin\RtoController::class, 'store'])->middleware('permission:orders.edit')->name('rto.store');
    Route::get('/rto/{rto}', [\App\Http\Controllers\Admin\RtoController::class, 'show'])->middleware('permission:orders.view')->name('rto.show');
    Route::post('/rto/{rto}/update-status', [\App\Http\Controllers\Admin\RtoController::class, 'updateStatus'])->middleware('permission:orders.edit')->name('rto.updateStatus');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // System Queue & Failed Jobs (Super Admin Restricted)
    Route::get('/system-health', [\App\Http\Controllers\Admin\SystemHealthController::class, 'index'])->name('system-health.index');
    Route::get('/system/failed-jobs', [\App\Http\Controllers\Admin\FailedJobController::class, 'index'])->name('system.failed-jobs.index');
    Route::post('/system/failed-jobs/retry-all', [\App\Http\Controllers\Admin\FailedJobController::class, 'retryAll'])->name('system.failed-jobs.retry-all');
    Route::post('/system/failed-jobs/{id}/retry', [\App\Http\Controllers\Admin\FailedJobController::class, 'retry'])->name('system.failed-jobs.retry');
    Route::delete('/system/failed-jobs/{id}', [\App\Http\Controllers\Admin\FailedJobController::class, 'destroy'])->name('system.failed-jobs.destroy');

    // System Backups (Super Admin Restricted)
    Route::get('/system/backups', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('system.backups.index');
    Route::post('/system/backups', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('system.backups.create');
    Route::get('/system/backups/{filename}/download', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('system.backups.download');
    Route::delete('/system/backups/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('system.backups.destroy');
});
