@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('meta_description', (string) ($product->meta_description ?: $product->short_description))
@section('canonical', $product->url)

@php
    $highlights = cms_lines($product->highlights);
    $specs = cms_pairs($product->specifications);
    $table = cms_table($product->data_table);
    $applications = cms_lines($product->applications);
@endphp

@section('content')
<x-page-hero :title="$product->name" :subtitle="$product->subtitle ?: $product->short_description" :crumbs="['Products' => route('products.index'), $category->name => route('products.category', $category)]" />

<section class="section">
    <div class="container-wide">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="row g-4 align-items-start mb-2">
                    <div class="col-md-6">
                        <x-media :src="$product->image" :alt="$product->name" :icon="$category->icon_class" class="ratio-4x3 rounded-media product-hero-img" :eager="true" />
                    </div>
                    <div class="col-md-6">
                        <span class="eyebrow">{{ $category->name }}</span>
                        <p class="product-lead">{{ $product->short_description }}</p>
                        @if ($highlights)
                            <ul class="tick-list">
                                @foreach ($highlights as $h)<li>{{ $h }}</li>@endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                @if ($product->description)
                    <div class="content-block">
                        <h2 class="sub-title">Overview</h2>
                        <div class="prose">{{ rich_text($product->description) }}</div>
                    </div>
                @endif

                @if ($specs)
                    <div class="content-block">
                        <h2 class="sub-title">Specifications</h2>
                        <dl class="spec-list">
                            @foreach ($specs as [$label, $value])
                                <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                @if (count($table) > 1)
                    <div class="content-block">
                        <h2 class="sub-title">{{ $product->data_table_title ?: 'Technical Data' }}</h2>
                        @include('partials.data-table', ['rows' => $table])
                    </div>
                @endif

                @if ($applications)
                    <div class="content-block">
                        <h2 class="sub-title">Applications</h2>
                        <ul class="app-chips">
                            @foreach ($applications as $app)<li><i class="fa-solid fa-circle-check"></i>{{ $app }}</li>@endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <aside class="col-lg-4">
                <div class="sticky-aside">
                    @include('products.partials.enquiry-sidebar', ['subject' => $product->name, 'productSlug' => $product->slug])
                    <div class="side-card mt-4">
                        <h3 class="side-title">Why WPI Products</h3>
                        <ul class="side-points">
                            <li><i class="fa-solid fa-certificate"></i>{{ setting('iso_label', 'ISO 9001:2015') }} certified manufacturing</li>
                            <li><i class="fa-solid fa-flask"></i>Documented chemistry &amp; hardness</li>
                            <li><i class="fa-solid fa-truck-fast"></i>Pan-India delivery from Ankleshwar</li>
                            <li><i class="fa-solid fa-headset"></i>Application engineering support</li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@if ($related->isNotEmpty())
<section class="section bg-soft">
    <div class="container-wide">
        <x-section-heading eyebrow="More from this category" :heading="'Related **Products**'" />
        <div class="row g-4">
            @foreach ($related as $item)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ $loop->index }}">@include('products.partials.product-card', ['product' => $item])</div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
