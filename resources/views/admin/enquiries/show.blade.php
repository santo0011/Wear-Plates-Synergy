@extends('layouts.admin')

@section('title', 'Enquiry #'.$enquiry->id)
@section('actions')
    <a href="{{ route('admin.enquiries.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
@endsection

@section('content')
@php
    $waNumber = preg_replace('/\D+/', '', $enquiry->mobile);
    if (strlen($waNumber) === 10) { $waNumber = '91'.$waNumber; } // assume an Indian mobile when no country code
    $waText = 'Hello '.$enquiry->name.', thank you for your enquiry ('.$enquiry->subject.') with '.setting('site_name', config('app.name')).'.';
@endphp
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2>{{ $enquiry->subject }}</h2>
                <span class="badge text-bg-{{ $enquiry->status_badge }} fs-6">{{ $enquiry->status_label }}</span>
            </div>
            <div class="card-body">
                <dl class="row dl-grid">
                    <div class="col-sm-6"><dt>Name</dt><dd>{{ $enquiry->name }}</dd></div>
                    <div class="col-sm-6"><dt>Company</dt><dd>{{ $enquiry->company_name ?: '—' }}</dd></div>
                    <div class="col-sm-6"><dt>Mobile</dt><dd><a href="{{ tel_url($enquiry->mobile) }}">{{ $enquiry->mobile }}</a></dd></div>
                    <div class="col-sm-6"><dt>Email</dt><dd>@if ($enquiry->email)<a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>@else — @endif</dd></div>
                    <div class="col-sm-6"><dt>Product</dt><dd>
                        @if ($enquiry->product && $enquiry->product->category)
                            <a href="{{ route('products.show', [$enquiry->product->category, $enquiry->product]) }}" target="_blank">{{ $enquiry->product->name }}</a>
                        @else — @endif
                    </dd></div>
                    <div class="col-sm-6"><dt>Received</dt><dd>{{ $enquiry->created_at->format('d M Y, h:i A') }}</dd></div>
                </dl>
                <h3 class="fs-6 text-uppercase text-muted fw-bold">Message</h3>
                <div class="p-3 bg-light rounded-3" style="white-space: pre-wrap">{{ $enquiry->message }}</div>
            </div>
            <div class="card-footer bg-white d-flex flex-wrap gap-2">
                <a href="{{ tel_url($enquiry->mobile) }}" class="btn btn-outline-maroon"><i class="fa-solid fa-phone me-1"></i>Call</a>
                @if ($waNumber)
                    <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener" class="btn btn-success"><i class="fa-brands fa-whatsapp me-1"></i>Reply on WhatsApp</a>
                @endif
                @if ($enquiry->email)
                    <a href="mailto:{{ $enquiry->email }}?subject={{ rawurlencode('Re: '.$enquiry->subject) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-reply me-1"></i>Reply by Email</a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h2>Update Status</h2></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            @foreach (\App\Models\Enquiry::STATUSES as $key => $s)
                                <option value="{{ $key }}" @selected(old('status', $enquiry->status) === $key)>{{ $s['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-admin.textarea name="admin_notes" label="Internal Notes" :value="$enquiry->admin_notes" rows="5" help="Visible to admins only." />
                    <button class="btn btn-maroon w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Save</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body small text-muted">
                <div><strong>IP:</strong> {{ $enquiry->ip_address ?: '—' }}</div>
                <div class="text-break"><strong>Browser:</strong> {{ $enquiry->user_agent ?: '—' }}</div>
                <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" class="mt-3" data-confirm-title="Delete this enquiry?" data-confirm="The enquiry from {{ $enquiry->name }} will be permanently deleted. This cannot be undone.">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger w-100"><i class="fa-solid fa-trash me-1"></i>Delete Enquiry</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
