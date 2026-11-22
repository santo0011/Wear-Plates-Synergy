<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/why-choose-us', [PageController::class, 'why'])->name('why');
Route::get('/our-presence', [PageController::class, 'presence'])->name('presence');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{category}', [ProductController::class, 'category'])->name('products.category');
Route::get('/products/{category}/{product}', [ProductController::class, 'show'])
    ->scopeBindings()
    ->name('products.show');

Route::get('/contact-us', [ContactController::class, 'show'])->name('contact');
Route::post('/contact-us', [ContactController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('contact.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('admin.nocache')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->name('login.attempt');

        Route::get('forgot-password', [Admin\PasswordResetController::class, 'showForgot'])->name('password.request');
        Route::post('forgot-password', [Admin\PasswordResetController::class, 'sendLink'])
            ->middleware('throttle:5,1')->name('password.email');
        Route::get('reset-password/{token}', [Admin\PasswordResetController::class, 'showReset'])->name('password.reset');
        Route::post('reset-password', [Admin\PasswordResetController::class, 'reset'])
            ->middleware('throttle:6,1')->name('password.update');
    });

    Route::middleware(['auth:admin', 'admin.active'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');

        Route::redirect('/', '/admin/dashboard');
        Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');

        Route::resource('categories', Admin\CategoryController::class)->except('show');
        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::resource('locations', Admin\LocationController::class)->except('show');

        Route::get('enquiries', [Admin\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('enquiries/{enquiry}', [Admin\EnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('enquiries/{enquiry}', [Admin\EnquiryController::class, 'update'])->name('enquiries.update');
        Route::delete('enquiries/{enquiry}', [Admin\EnquiryController::class, 'destroy'])->name('enquiries.destroy');

        Route::get('settings/{group?}', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings/{group}', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('settings-mail/test', [Admin\SettingController::class, 'testMail'])
            ->middleware('throttle:5,1')->name('settings.mail.test');

        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
    });
});
