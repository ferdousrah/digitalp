@extends('layouts.app')
@section('title', 'Our Services - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Services']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-display mb-4">Our Services</h1>
        <p class="text-surface-600 max-w-2xl mx-auto">We provide comprehensive digital product services to meet all your technology needs.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        @foreach($services as $service)
        <div class="gsap-fade-up bg-white rounded-xl p-8 shadow-sm border border-surface-200 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 bg-primary-50 rounded-lg flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <h2 class="text-xl font-semibold text-surface-900 mb-3">{{ $service->title }}</h2>
            <p class="text-surface-600 mb-4">{{ $service->short_description }}</p>
            <a href="{{ route('services.show', $service) }}" class="text-primary-600 font-medium hover:text-primary-700 inline-flex items-center gap-1">
                Learn More <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
