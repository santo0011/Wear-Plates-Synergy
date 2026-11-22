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
                ->whereHas('category', fn ($q) => $q->active())->take(8)->get(),
            'locations' => Location::active()->ordered()->get(),
        ]);
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function leadership(): View
    {
        return view('pages.leadership');
    }

    public function industries(): View
    {
        return view('pages.industries');
    }

    public function infrastructure(): View
    {
        return view('pages.infrastructure');
    }

    public function quality(): View
    {
        return view('pages.quality');
    }

    public function projects(): View
    {
        return view('pages.projects');
    }

    public function resources(): View
    {
        return view('pages.resources', [
            'products' => Product::active()->ordered()->with('category')
                ->whereHas('category', fn ($q) => $q->active())
                ->whereNotNull('data_table')->where('data_table', '!=', '')->get()
                ->sortBy(fn ($p) => [$p->category->sort_order, $p->sort_order])->values(),
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
