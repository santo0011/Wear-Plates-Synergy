@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
@php
    $admin = auth('admin')->user();
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $total = max(1, $stats['enquiries']);
@endphp

{{-- Welcome --}}
<div class="welcome mb-4">
    <div class="flex-grow-1">
        <h2>{{ $greeting }}, {{ $admin->name }}</h2>
        <p>
            @if ($stats['new_enquiries'])
                You have <strong class="text-white">{{ $stats['new_enquiries'] }} new {{ \Illuminate\Support\Str::plural('enquiry', $stats['new_enquiries']) }}</strong> waiting for a reply.
            @else
                You're all caught up – no new enquiries right now.
            @endif
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.enquiries.index', $stats['new_enquiries'] ? ['status' => 'new'] : []) }}" class="btn btn-light"><i class="fa-solid fa-inbox me-2"></i>View Enquiries</a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-ghost"><i class="fa-solid fa-plus me-2"></i>Add Product</a>
    </div>
</div>

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.enquiries.index', ['status' => 'new']) }}" class="stat">
            <div class="stat-top"><span class="stat-label">New Enquiries</span><span class="stat-icon"><i class="fa-solid fa-bell"></i></span></div>
            <div class="stat-value">{{ $stats['new_enquiries'] }}</div>
            <div class="stat-foot {{ $stats['new_enquiries'] ? 'alert-dot' : '' }}">{{ $stats['new_enquiries'] ? 'Needs attention' : 'Nothing pending' }}</div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.enquiries.index') }}" class="stat">
            <div class="stat-top"><span class="stat-label">Total Enquiries</span><span class="stat-icon blue"><i class="fa-solid fa-inbox"></i></span></div>
            <div class="stat-value">{{ $stats['enquiries'] }}</div>
            <div class="stat-foot">{{ $stats['this_month'] }} this month</div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.products.index') }}" class="stat">
            <div class="stat-top"><span class="stat-label">Products</span><span class="stat-icon gold"><i class="fa-solid fa-box"></i></span></div>
            <div class="stat-value">{{ $stats['products'] }}</div>
            <div class="stat-foot">In {{ $stats['categories'] }} categories</div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('admin.locations.index') }}" class="stat">
            <div class="stat-top"><span class="stat-label">Locations</span><span class="stat-icon green"><i class="fa-solid fa-location-dot"></i></span></div>
            <div class="stat-value">{{ $stats['locations'] }}</div>
            <div class="stat-foot">Offices &amp; plant</div>
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Recent enquiries --}}
    <div class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Recent Enquiries</h2>
                <a href="{{ route('admin.enquiries.index') }}" class="btn btn-sm btn-outline-maroon">View all</a>
            </div>
            @forelse ($recentEnquiries as $enquiry)
                <a href="{{ route('admin.enquiries.show', $enquiry) }}" @class(['enq-row', 'is-new' => ! $enquiry->read_at])>
                    <span class="avatar">{{ strtoupper(mb_substr($enquiry->name, 0, 1)) }}</span>
                    <span class="meta">
                        <strong>{{ $enquiry->name }}@if ($enquiry->company_name) <span class="fw-normal text-muted">· {{ $enquiry->company_name }}</span>@endif</strong>
                        <small>{{ $enquiry->subject }}</small>
                    </span>
                    <span class="badge text-bg-{{ $enquiry->status_badge }} d-none d-sm-inline">{{ $enquiry->status_label }}</span>
                    <span class="when">{{ $enquiry->created_at->diffForHumans(null, true, true) }}</span>
                </a>
            @empty
                <div class="empty-state">
                    <i class="fa-regular fa-envelope-open"></i>
                    No enquiries yet. New enquiries from the website will appear here.
                </div>
            @endforelse
        </div>
    </div>

    <div class="col-xl-4 d-flex flex-column gap-4">
        {{-- Status breakdown --}}
        <div class="card">
            <div class="card-header"><h2>Enquiry Status</h2></div>
            <div class="card-body">
                @foreach (\App\Models\Enquiry::STATUSES as $key => $s)
                    @php $count = (int) ($statusCounts[$key] ?? 0); @endphp
                    <a href="{{ route('admin.enquiries.index', ['status' => $key]) }}" class="status-row d-block text-reset">
                        <div class="d-flex justify-content-between"><span>{{ $s['label'] }}</span><strong>{{ $count }}</strong></div>
                        <div class="progress"><div class="progress-bar bg-{{ $s['badge'] }}" style="width: {{ $stats['enquiries'] ? round($count / $total * 100) : 0 }}%"></div></div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Quick links --}}
        <div class="card">
            <div class="card-header"><h2>Quick Links</h2></div>
            <a href="{{ route('admin.products.create') }}" class="quick-link"><i class="fa-solid fa-plus"></i>Add a product<i class="fa-solid fa-chevron-right"></i></a>
            <a href="{{ route('admin.categories.create') }}" class="quick-link"><i class="fa-solid fa-layer-group"></i>Add a category<i class="fa-solid fa-chevron-right"></i></a>
            <a href="{{ route('admin.settings.edit', 'contact') }}" class="quick-link"><i class="fa-solid fa-phone"></i>Edit contact details<i class="fa-solid fa-chevron-right"></i></a>
            <a href="{{ route('admin.settings.edit', 'home') }}" class="quick-link"><i class="fa-solid fa-house"></i>Edit home page text<i class="fa-solid fa-chevron-right"></i></a>
        </div>
    </div>
</div>
@endsection
