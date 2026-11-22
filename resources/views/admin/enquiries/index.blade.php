@extends('layouts.admin')

@section('title', 'Customer Enquiries')

@section('content')
<div class="status-pills d-flex flex-wrap gap-2 mb-3">
    <a href="{{ route('admin.enquiries.index', request()->only('q')) }}" class="btn btn-sm {{ request('status') ? 'btn-light' : 'btn-maroon' }}">All <span class="badge text-bg-light ms-1">{{ $statusCounts->sum() }}</span></a>
    @foreach (\App\Models\Enquiry::STATUSES as $key => $s)
        <a href="{{ route('admin.enquiries.index', ['status' => $key] + request()->only('q')) }}" class="btn btn-sm {{ request('status') === $key ? 'btn-maroon' : 'btn-light' }}">{{ $s['label'] }} <span class="badge text-bg-{{ $s['badge'] }} ms-1">{{ $statusCounts[$key] ?? 0 }}</span></a>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <form class="d-flex gap-2" method="GET">
            @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, company, mobile, email, subject…" style="max-width: 420px">
            <button class="btn btn-outline-maroon"><i class="fa-solid fa-magnifying-glass"></i></button>
            @if (request('q'))<a href="{{ route('admin.enquiries.index', request()->only('status')) }}" class="btn btn-light">Clear</a>@endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>#</th><th>Name / Company</th><th>Contact</th><th>Subject</th><th>Status</th><th>Received</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($enquiries as $enquiry)
                <tr class="{{ $enquiry->read_at ? '' : 'unread' }}">
                    <td>{{ $enquiry->id }}</td>
                    <td>{{ $enquiry->name }}<div class="small text-muted fw-normal">{{ $enquiry->company_name }}</div></td>
                    <td class="small">{{ $enquiry->mobile }}<div class="text-muted fw-normal">{{ $enquiry->email }}</div></td>
                    <td>{{ \Illuminate\Support\Str::limit($enquiry->subject, 50) }}
                        @if ($enquiry->product)<div class="small text-muted fw-normal"><i class="fa-solid fa-box me-1"></i>{{ $enquiry->product->name }}</div>@endif
                    </td>
                    <td><span class="badge text-bg-{{ $enquiry->status_badge }}">{{ $enquiry->status_label }}</span></td>
                    <td class="small text-muted text-nowrap" title="{{ $enquiry->created_at->format('d M Y, h:i A') }}">{{ $enquiry->created_at->format('d M Y') }}<div>{{ $enquiry->created_at->diffForHumans() }}</div></td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="btn btn-sm btn-outline-maroon" title="Open"><i class="fa-solid fa-envelope-open-text"></i></a>
                        <form action="{{ route('admin.enquiries.destroy', $enquiry) }}" method="POST" class="d-inline" data-confirm-title="Delete this enquiry?" data-confirm="The enquiry from {{ $enquiry->name }} will be permanently deleted. This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fs-2 d-block mb-2"></i>No enquiries found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($enquiries->hasPages())<div class="card-body">{{ $enquiries->links() }}</div>@endif
</div>
@endsection
