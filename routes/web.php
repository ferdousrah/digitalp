<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\SslcommerzController;
use App\Http\Controllers\Auth\PhoneAuthController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

// Admin — invoice + label printable views (auth-gated in controller)
Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\OrderInvoiceController::class, 'show'])
        ->name('orders.invoice');
    Route::get('/orders/{order}/label', [\App\Http\Controllers\Admin\OrderInvoiceController::class, 'label'])
        ->name('orders.label');
    Route::get('/orders-labels', [\App\Http\Controllers\Admin\OrderInvoiceController::class, 'labels'])
        ->name('orders.labels');
});

// SEO
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search');
Route::get('/search/autocomplete', [SearchController::class, 'autocomplete'])->name('search.autocomplete');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product:slug}/quick-view', [ProductController::class, 'quickView'])->name('products.quickView');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Compare
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');
Route::post('/compare/add/{product}', [CompareController::class, 'add'])->name('compare.add');
Route::delete('/compare/remove/{product}', [CompareController::class, 'remove'])->name('compare.remove');
Route::post('/compare/clear', [CompareController::class, 'clear'])->name('compare.clear');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'data'])->name('cart.data');
Route::get('/cart/suggestions', [CartController::class, 'suggestions'])->name('cart.suggestions');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{key}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Wishlist
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{blogCategory:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{blogPost:slug}', [BlogController::class, 'show'])->name('blog.show');

// Services
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

// Contact
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// Gallery
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{galleryAlbum:slug}', [GalleryController::class, 'show'])->name('gallery.show');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Customer auth — phone + OTP
Route::get('/login',           [PhoneAuthController::class, 'showLogin'])->name('login');
Route::post('/auth/otp/send',  [PhoneAuthController::class, 'sendOtp'])->name('auth.otp.send');
Route::post('/auth/otp/verify',[PhoneAuthController::class, 'verifyOtp'])->name('auth.otp.verify');
Route::post('/logout',         [PhoneAuthController::class, 'logout'])->name('logout');

// PWA manifest — generated dynamically from SettingService so changes in /admin take effect without a redeploy.
Route::get('/manifest.webmanifest', function () {
    $name      = \App\Services\SettingService::get('site_name', config('app.name'));
    $shortName = mb_substr(\App\Services\SettingService::get('site_short_name', $name), 0, 12);
    $logoKey   = \App\Services\SettingService::get('site_logo');
    $logoUrl   = $logoKey ? \Illuminate\Support\Facades\Storage::disk('public')->url($logoKey) : asset('favicon.ico');
    $tc = \App\Filament\Pages\TemplateSettings::defaults();
    $themeColor = \App\Services\SettingService::get('color_primary', $tc['color_primary'] ?? '#16a34a');

    return response()->json([
        'name'             => $name,
        'short_name'       => $shortName,
        'description'      => \App\Services\SettingService::get('site_description', 'Your trusted partner for digital products and computer accessories.'),
        'start_url'        => url('/'),
        'scope'            => url('/'),
        'display'          => 'standalone',
        'orientation'      => 'portrait-primary',
        'theme_color'      => $themeColor,
        'background_color' => '#ffffff',
        'lang'             => 'en',
        'icons' => [
            [ 'src' => $logoUrl, 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any' ],
            [ 'src' => $logoUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any' ],
            [ 'src' => $logoUrl, 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable' ],
        ],
        'shortcuts' => [
            [ 'name' => 'Products', 'url' => url('/products'), 'description' => 'Browse all products' ],
            [ 'name' => 'My account', 'url' => url('/account'), 'description' => 'View your orders and profile' ],
            [ 'name' => 'Track order', 'url' => url('/track-order'), 'description' => 'Track an existing order' ],
        ],
    ])->header('Content-Type', 'application/manifest+json')
      ->header('Cache-Control', 'public, max-age=86400');
})->name('pwa.manifest');

// Customer account (auth required)
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/',                       [AccountController::class, 'index'])->name('index');
    Route::get('/orders',                 [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}',   [AccountController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [AccountController::class, 'cancelOrder'])->name('orders.cancel');
    Route::post('/profile',               [AccountController::class, 'updateProfile'])->name('profile.update');
});

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/thanas', [CheckoutController::class, 'thanas'])->name('checkout.thanas');
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.coupon');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/invoice/{orderNumber}', [CheckoutController::class, 'invoice'])->name('checkout.invoice');
Route::get('/bkash/callback', [BkashController::class, 'callback'])->name('bkash.callback');

// SSLCommerz — POST callbacks exempt from CSRF via bootstrap/app.php
Route::post('/sslcommerz/success', [SslcommerzController::class, 'success'])->name('sslcommerz.success');
Route::post('/sslcommerz/fail',    [SslcommerzController::class, 'fail'])->name('sslcommerz.fail');
Route::post('/sslcommerz/cancel',  [SslcommerzController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn',     [SslcommerzController::class, 'ipn'])->name('sslcommerz.ipn');

// Track Order
Route::get('/track-order', [\App\Http\Controllers\TrackOrderController::class, 'index'])->name('track-order.index');
Route::post('/track-order', [\App\Http\Controllers\TrackOrderController::class, 'track'])->name('track-order.track');

// Language switcher
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'bn'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

// Static Pages
Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');
