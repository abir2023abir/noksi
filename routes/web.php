<?php

use App\Http\Controllers\Account;
use App\Http\Controllers\Auth;
use App\Http\Controllers\Shop;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', [Shop\HomeController::class, 'index'])->name('home');
Route::get('/locale/{locale}', [Shop\HomeController::class, 'locale'])->name('locale');

Route::get('/shop', [Shop\ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/category/{category:slug}', [Shop\ShopController::class, 'category'])->name('shop.category');
Route::get('/shop/brand/{brand:slug}', [Shop\ShopController::class, 'brand'])->name('shop.brand');
Route::get('/collections/{collection:slug}', [Shop\ShopController::class, 'collection'])->name('shop.collection');
Route::get('/new-arrivals', [Shop\ShopController::class, 'newArrivals'])->name('shop.new');
Route::get('/sale', [Shop\ShopController::class, 'sale'])->name('shop.sale');
Route::get('/flash-sale', [Shop\ShopController::class, 'flashSale'])->name('shop.flash');
Route::get('/search', [Shop\ShopController::class, 'search'])->name('search');
Route::get('/search/suggest', [Shop\ShopController::class, 'suggest'])->name('search.suggest');

Route::get('/product/{product:slug}', [Shop\ProductController::class, 'show'])->name('product.show');
Route::get('/product/{product:slug}/quick-view', [Shop\ProductController::class, 'quickView'])->name('product.quick');
Route::post('/product/{product:slug}/review', [Shop\ProductController::class, 'review'])->name('product.review');

Route::controller(Shop\CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::post('/cart/add', 'add')->name('cart.add');
    Route::post('/cart/update', 'update')->name('cart.update');
    Route::post('/cart/remove', 'remove')->name('cart.remove');
    Route::post('/cart/coupon', 'applyCoupon')->name('cart.coupon');
    Route::delete('/cart/coupon', 'removeCoupon')->name('cart.coupon.remove');
});

Route::controller(Shop\WishlistController::class)->group(function () {
    Route::get('/wishlist', 'index')->name('wishlist.index');
    Route::post('/wishlist/toggle', 'toggle')->name('wishlist.toggle');
});

Route::controller(Shop\CheckoutController::class)->group(function () {
    Route::get('/checkout', 'index')->name('checkout.index');
    Route::post('/checkout', 'store')->name('checkout.store');
    Route::get('/checkout/shipping-methods', 'shippingMethods')->name('checkout.shipping');
    Route::get('/order/success/{order:order_number}', 'success')->name('checkout.success');
    Route::get('/order/pay/{order:order_number}', 'pay')->name('checkout.pay');
});

Route::controller(Shop\PaymentController::class)->prefix('payment/sslcommerz')->name('payment.sslcommerz.')->group(function () {
    Route::post('success', 'success')->name('success');
    Route::post('fail', 'fail')->name('fail');
    Route::post('cancel', 'cancel')->name('cancel');
    Route::post('ipn', 'ipn')->name('ipn');
});

Route::get('/track', [Shop\OrderTrackingController::class, 'index'])->name('order.track.index');
Route::get('/track/{number}', [Shop\OrderTrackingController::class, 'show'])->name('order.track');

Route::get('/blog', [Shop\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [Shop\BlogController::class, 'show'])->name('blog.show');

Route::get('/contact', [Shop\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [Shop\PageController::class, 'sendContact'])->name('contact.send');
Route::get('/faq', [Shop\PageController::class, 'faq'])->name('faq');
Route::get('/about', [Shop\PageController::class, 'about'])->name('about');
Route::post('/newsletter', [Shop\PageController::class, 'newsletter'])->name('newsletter');
Route::get('/pages/{page:slug}', [Shop\PageController::class, 'show'])->name('page.show');

/*
|--------------------------------------------------------------------------
| Auth (customers)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [Auth\LoginController::class, 'create'])->name('login');
    Route::post('/login', [Auth\LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [Auth\RegisterController::class, 'create'])->name('register');
    Route::post('/register', [Auth\RegisterController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/forgot-password', [Auth\PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [Auth\PasswordResetController::class, 'email'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [Auth\PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [Auth\PasswordResetController::class, 'update'])->name('password.update');
});
Route::post('/logout', [Auth\LoginController::class, 'destroy'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Customer account
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [Account\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [Account\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{order:order_number}', [Account\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order:order_number}/cancel', [Account\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::get('/orders/{order:order_number}/invoice', [Account\OrderController::class, 'invoice'])->name('orders.invoice');
    Route::resource('addresses', Account\AddressController::class)->except(['show', 'create', 'edit']);
    Route::get('/profile', [Account\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [Account\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [Account\ProfileController::class, 'password'])->name('password');
    Route::get('/wishlist', [Shop\WishlistController::class, 'index'])->name('wishlist');
    Route::get('/reviews', [Account\DashboardController::class, 'reviews'])->name('reviews');
});

require __DIR__.'/admin.php';
