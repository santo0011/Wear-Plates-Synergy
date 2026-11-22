<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Services\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private ImageUploader $uploader) {}

    public function index(Request $request): View
    {
        $categories = ProductCategory::withCount('products')
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->q.'%'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.form', ['category' => new ProductCategory(['is_active' => true, 'icon' => 'fa-solid fa-industry'])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->store($request->file('image'), 'categories');
        }
        ProductCategory::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ProductCategory $category): View
    {
        return view('admin.categories.form', compact('category'));
    }

    public function update(Request $request, ProductCategory $category): RedirectResponse
    {
        $data = $this->validated($request, $category);

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->replace($category->image, $request->file('image'), 'categories');
        } elseif ($request->boolean('remove_image')) {
            $this->uploader->delete($category->image);
            $data['image'] = null;
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'This category still has products. Move or delete them first.');
        }

        $this->uploader->delete($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?ProductCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('product_categories', 'slug')->ignore($category?->id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'icon' => ['nullable', 'string', 'max:80', 'regex:/^[a-z0-9\- ]+$/'],
            'image' => ImageUploader::RULES,
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ], ['icon.regex' => 'Icon must be a Font Awesome class such as "fa-solid fa-shield".']);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        unset($data['image']);

        return $data;
    }
}
