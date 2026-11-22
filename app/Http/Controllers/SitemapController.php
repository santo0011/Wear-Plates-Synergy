<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect(['home', 'about', 'products.index', 'why', 'presence', 'contact'])
            ->map(fn ($name) => ['loc' => route($name), 'lastmod' => null]);

        ProductCategory::active()->ordered()->with('activeProducts')->get()->each(function ($category) use ($urls) {
            $urls->push(['loc' => route('products.category', $category), 'lastmod' => $category->updated_at]);
            foreach ($category->activeProducts as $product) {
                $urls->push(['loc' => route('products.show', [$category, $product]), 'lastmod' => $product->updated_at]);
            }
        });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
