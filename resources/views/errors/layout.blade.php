@extends('layouts.app')

@section('title', $title)

@push('head')
    <meta name="robots" content="noindex">
@endpush

@section('content')
<section class="error-page">
    <div>
        <div class="error-code">{{ $code }}</div>
        <h1 class="text-uppercase mb-3">{{ $title }}</h1>
        <p class="text-muted mb-4 mx-auto" style="max-width: 520px">{{ $message }}</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="{{ route('home') }}" class="btn btn-maroon"><i class="fa-solid fa-house me-2"></i>Back to Home</a>
            <a href="{{ route('products.index') }}" class="btn btn-outline-maroon">Our Products</a>
            <a href="{{ route('contact') }}" class="btn btn-outline-maroon">Contact Us</a>
        </div>
    </div>
</section>
@endsection
