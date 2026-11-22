@extends('layouts.app')

@section('title', 'Why Choose Us')
@section('meta_description', (string) (setting('seo_why_description')))

@section('content')
<x-page-hero :title="setting('why_heading', 'Why Choose Us')" :subtitle="setting('why_intro')" :breadcrumbs="['Why Choose Us' => null]" />

<section class="section">
    <div class="container">
        <x-section-heading eyebrow="Our Strengths" title='What Sets <span class="hl">Us Apart</span>' />
        <x-why-grid />
    </div>
</section>

<section class="section bg-light-gray">
    <div class="container"><x-md-message /></div>
</section>

<section class="section">
    <div class="container"><x-delivery-band /></div>
</section>

<section class="section pt-0">
    <div class="container"><x-cta-band /></div>
</section>
@endsection
