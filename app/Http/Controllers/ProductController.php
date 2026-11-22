<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('products.index', [
            'categories' => ProductCategory::active()->ordered()->with('activeProducts')->get(),
        ]);
    }

    public function category(ProductCategory $category): View
    {
        abort_unless($category->is_active, 404);

        return view('products.category', [
            'category' => $category,
            'products' => $category->activeProducts()->get(),
            'otherCategories' => ProductCategory::active()->ordered()->whereKeyNot($category->id)->get(),
        ]);
    }

    public function show(ProductCategory $category, Product $product): View
    {
        abort_unless($category->is_active && $product->is_active, 404);
        $product->setRelation('category', $category);

        return view('products.show', [
            'category' => $category,
            'product' => $product,
            'related' => $category->activeProducts()->whereKeyNot($product->id)->take(3)->get()
                ->each->setRelation('category', $category),
        ]);
    }
}
