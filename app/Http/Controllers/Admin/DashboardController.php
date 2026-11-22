<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $statusCounts = Enquiry::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'stats' => [
                'categories' => ProductCategory::count(),
                'products' => Product::count(),
                'enquiries' => Enquiry::count(),
                'new_enquiries' => (int) ($statusCounts['new'] ?? 0),
                'locations' => Location::count(),
                'this_month' => Enquiry::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'statusCounts' => $statusCounts,
            'recentEnquiries' => Enquiry::with('product')->latest()->take(8)->get(),
        ]);
    }
}
