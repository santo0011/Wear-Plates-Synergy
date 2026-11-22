<?php

namespace App\Providers;

use App\Models\Enquiry;
use App\Models\ProductCategory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();

        // Use the SMTP settings managed in the admin panel (falls back to .env).
        \App\Support\MailSettings::apply();

        // Product categories for the public header/footer menus.
        View::composer(['layouts.app', 'partials.header', 'partials.footer'], function ($view) {
            static $categories = null;
            if ($categories === null) {
                try {
                    $categories = ProductCategory::active()->ordered()->get(['id', 'name', 'slug', 'icon', 'short_description']);
                } catch (Throwable) {
                    $categories = collect();
                }
            }
            $view->with('navCategories', $categories);
        });

        // Unread enquiry badge for the admin sidebar.
        View::composer('layouts.admin', function ($view) {
            $view->with('newEnquiryCount', Enquiry::where('status', 'new')->count());
        });
    }
}
