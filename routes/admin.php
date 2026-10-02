<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'create'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'store'])->middleware('throttle:10,1');
    });

    Route::middleware(['auth', 'staff'])->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'destroy'])->name('logout');
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart', [Admin\DashboardController::class, 'chart'])->name('dashboard.chart');
        Route::get('/search', [Admin\DashboardController::class, 'search'])->name('search');
        Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

        // Catalog
        Route::middleware('permission:products.manage')->group(function () {
            Route::get('products/create', [Admin\ProductController::class, 'create'])->name('products.create');
            Route::post('products', [Admin\ProductController::class, 'store'])->name('products.store');
            Route::get('products/{product}/edit', [Admin\ProductController::class, 'edit'])->name('products.edit');
            Route::put('products/{product}', [Admin\ProductController::class, 'update'])->name('products.update');
            Route::patch('products/{product}', [Admin\ProductController::class, 'update']);
            Route::delete('products/{product}', [Admin\ProductController::class, 'destroy'])->name('products.destroy');
            Route::post('products/{product}/images', [Admin\ProductController::class, 'uploadImages'])->name('products.images.store');
            Route::delete('products/{product}/images/{image}', [Admin\ProductController::class, 'deleteImage'])->name('products.images.destroy');
            Route::post('products/{product}/images/{image}/primary', [Admin\ProductController::class, 'primaryImage'])->name('products.images.primary');
            Route::post('products/{product}/images/reorder', [Admin\ProductController::class, 'reorderImages'])->name('products.images.reorder');
            Route::post('products/{product}/variants', [Admin\ProductController::class, 'saveVariants'])->name('products.variants');
            Route::post('products/{product}/duplicate', [Admin\ProductController::class, 'duplicate'])->name('products.duplicate');
            Route::post('products/bulk', [Admin\ProductController::class, 'bulk'])->name('products.bulk');
            Route::post('products/{product}/toggle', [Admin\ProductController::class, 'toggle'])->name('products.toggle');
        });
        Route::middleware('permission:products.view')->group(function () {
            Route::get('products', [Admin\ProductController::class, 'index'])->name('products.index');
            Route::get('products/{product}', [Admin\ProductController::class, 'show'])->name('products.show');
        });
        Route::resource('categories', Admin\CategoryController::class)->except('show')->middleware('permission:categories.manage');
        Route::post('categories/reorder', [Admin\CategoryController::class, 'reorder'])->name('categories.reorder')->middleware('permission:categories.manage');
        Route::resource('brands', Admin\BrandController::class)->except('show')->middleware('permission:brands.manage');
        Route::resource('attributes', Admin\AttributeController::class)->except('show')->middleware('permission:attributes.manage');
        Route::post('attributes/{attribute}/values', [Admin\AttributeController::class, 'storeValue'])->name('attributes.values.store')->middleware('permission:attributes.manage');
        Route::delete('attributes/{attribute}/values/{value}', [Admin\AttributeController::class, 'destroyValue'])->name('attributes.values.destroy')->middleware('permission:attributes.manage');
        Route::resource('collections', Admin\CollectionController::class)->except('show')->middleware('permission:collections.manage');
        Route::middleware('permission:inventory.manage')->group(function () {
            Route::get('inventory', [Admin\InventoryController::class, 'index'])->name('inventory.index');
            Route::post('inventory/adjust', [Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');
            Route::get('inventory/movements', [Admin\InventoryController::class, 'movements'])->name('inventory.movements');
        });

        // Sales
        Route::middleware('permission:orders.view')->group(function () {
            Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/export', [Admin\OrderController::class, 'export'])->name('orders.export');
            Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
            Route::get('orders/{order}/invoice', [Admin\OrderController::class, 'invoice'])->name('orders.invoice');
        });
        Route::middleware('permission:orders.manage')->group(function () {
            Route::post('orders/{order}/status', [Admin\OrderController::class, 'updateStatus'])->name('orders.status');
            Route::post('orders/{order}/payment', [Admin\OrderController::class, 'updatePayment'])->name('orders.payment');
            Route::post('orders/{order}/tracking', [Admin\OrderController::class, 'updateTracking'])->name('orders.tracking');
            Route::post('orders/{order}/notes', [Admin\OrderController::class, 'updateNotes'])->name('orders.notes');
        });
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');
        });
        Route::post('customers/{customer}/toggle', [Admin\CustomerController::class, 'toggle'])->name('customers.toggle')->middleware('permission:customers.manage');
        Route::resource('coupons', Admin\CouponController::class)->except('show')->middleware('permission:coupons.manage');
        Route::resource('flash-sales', Admin\FlashSaleController::class)->middleware('permission:flash-sales.manage');
        Route::post('flash-sales/{flash_sale}/products', [Admin\FlashSaleController::class, 'addProduct'])->name('flash-sales.products.store')->middleware('permission:flash-sales.manage');
        Route::delete('flash-sales/{flash_sale}/products/{item}', [Admin\FlashSaleController::class, 'removeProduct'])->name('flash-sales.products.destroy')->middleware('permission:flash-sales.manage');
        Route::middleware('permission:reviews.manage')->group(function () {
            Route::get('reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
            Route::post('reviews/{review}/status', [Admin\ReviewController::class, 'status'])->name('reviews.status');
            Route::post('reviews/{review}/reply', [Admin\ReviewController::class, 'reply'])->name('reviews.reply');
            Route::delete('reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
        });

        // Content
        Route::resource('banners', Admin\BannerController::class)->except('show')->middleware('permission:banners.manage');
        Route::resource('testimonials', Admin\TestimonialController::class)->except('show')->middleware('permission:testimonials.manage');
        Route::middleware('permission:blog.manage')->group(function () {
            Route::resource('posts', Admin\PostController::class)->except('show');
            Route::resource('blog-categories', Admin\BlogCategoryController::class)->only(['store', 'update', 'destroy']);
        });
        Route::resource('pages', Admin\PageController::class)->except('show')->middleware('permission:pages.manage');
        Route::resource('faqs', Admin\FaqController::class)->except('show')->middleware('permission:faqs.manage');
        Route::middleware('permission:shipping.manage')->group(function () {
            Route::get('shipping', [Admin\ShippingController::class, 'index'])->name('shipping.index');
            Route::post('shipping/zones', [Admin\ShippingController::class, 'storeZone'])->name('shipping.zones.store');
            Route::put('shipping/zones/{zone}', [Admin\ShippingController::class, 'updateZone'])->name('shipping.zones.update');
            Route::delete('shipping/zones/{zone}', [Admin\ShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');
            Route::post('shipping/methods', [Admin\ShippingController::class, 'storeMethod'])->name('shipping.methods.store');
            Route::put('shipping/methods/{method}', [Admin\ShippingController::class, 'updateMethod'])->name('shipping.methods.update');
            Route::delete('shipping/methods/{method}', [Admin\ShippingController::class, 'destroyMethod'])->name('shipping.methods.destroy');
        });

        // Marketing / communication
        Route::middleware('permission:newsletter.manage')->group(function () {
            Route::get('newsletter', [Admin\NewsletterController::class, 'index'])->name('newsletter.index');
            Route::get('newsletter/export', [Admin\NewsletterController::class, 'export'])->name('newsletter.export');
            Route::delete('newsletter/{subscriber}', [Admin\NewsletterController::class, 'destroy'])->name('newsletter.destroy');
        });
        Route::middleware('permission:messages.manage')->group(function () {
            Route::get('messages', [Admin\MessageController::class, 'index'])->name('messages.index');
            Route::get('messages/{message}', [Admin\MessageController::class, 'show'])->name('messages.show');
            Route::post('messages/{message}/reply', [Admin\MessageController::class, 'reply'])->name('messages.reply');
            Route::delete('messages/{message}', [Admin\MessageController::class, 'destroy'])->name('messages.destroy');
        });

        // Reports
        Route::middleware('permission:reports.view')->group(function () {
            Route::get('reports', [Admin\ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/sales', [Admin\ReportController::class, 'sales'])->name('reports.sales');
            Route::get('reports/products', [Admin\ReportController::class, 'products'])->name('reports.products');
            Route::get('reports/customers', [Admin\ReportController::class, 'customers'])->name('reports.customers');
        });

        // System
        Route::middleware('permission:settings.manage')->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'index'])->name('settings.index');
            Route::post('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
            Route::post('settings/cache', [Admin\SettingController::class, 'clearCache'])->name('settings.cache');
        });
        Route::middleware('permission:staff.manage')->group(function () {
            Route::resource('staff', Admin\StaffController::class)->except('show');
            Route::resource('roles', Admin\RoleController::class)->except('show');
        });
        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index')->middleware('permission:logs.view');
    });
});
