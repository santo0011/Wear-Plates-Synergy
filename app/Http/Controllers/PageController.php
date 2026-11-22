<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'categories' => ProductCategory::active()->ordered()->withCount(['products' => fn ($q) => $q->active()])->get(),
            'featuredProducts' => Product::active()->featured()->ordered()->with('category')
                ->whereHas('category', fn ($q) => $q->active())->take(6)->get(),
            'locations' => Location::active()->ordered()->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'categories' => ProductCategory::active()->ordered()->get(),
        ]);
    }

    public function why(): View
    {
        return view('pages.why');
    }

    public function presence(): View
    {
        return view('pages.presence', [
            'locations' => Location::active()->ordered()->get(),
        ]);
    }
}
