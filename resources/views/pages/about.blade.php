@extends('layouts.app')
@section('title', $page?->meta_title ?? 'About Us')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'About Us']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-3xl mx-auto mb-16">
        <h1 class="text-3xl md:text-4xl font-display mb-6 text-center">About {{ \App\Services\SettingService::get('site_name', config('app.name')) }}</h1>
        @if($page?->content)<div class="prose prose-lg max-w-none">{!! $page->content !!}</div>@endif
    </div>

    <!-- Timeline -->
    @if($timeline->count())
    <div class="max-w-3xl mx-auto mb-16">
        <h2 class="text-2xl font-display mb-8 text-center gsap-fade-up">Our Journey</h2>
        <div class="relative">
            <div class="absolute left-1/2 transform -translate-x-0.5 h-full w-0.5 bg-primary-200 hidden md:block"></div>
            @foreach($timeline as $i => $event)
            <div class="gsap-fade-up relative flex flex-col md:flex-row items-center mb-12 {{ $i % 2 == 0 ? 'md:flex-row' : 'md:flex-row-reverse' }}">
                <div class="md:w-1/2 {{ $i % 2 == 0 ? 'md:pr-12 md:text-right' : 'md:pl-12' }}">
                    <span class="text-primary-600 font-bold text-lg">{{ $event->year }}</span>
                    <h3 class="font-semibold text-xl mt-1 mb-2">{{ $event->title }}</h3>
                    <p class="text-surface-600">{{ $event->description }}</p>
                </div>
                <div class="absolute left-1/2 transform -translate-x-1/2 w-4 h-4 bg-primary-500 rounded-full border-4 border-white shadow hidden md:block"></div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Team -->
    @if($team->count())
    <div>
        <h2 class="text-2xl font-display mb-8 text-center gsap-fade-up">Meet Our Team</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($team as $member)
            <div class="gsap-fade-up text-center">
                <div class="w-32 h-32 mx-auto mb-4 rounded-full bg-surface-200 overflow-hidden">
                    @if($member->getFirstMediaUrl('member_photo'))<img src="{{ $member->getFirstMediaUrl('member_photo') }}" alt="{{ $member->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover">@endif
                </div>
                <h3 class="font-semibold text-lg">{{ $member->name }}</h3>
                <p class="text-primary-600 text-sm mb-2">{{ $member->position }}</p>
                @if($member->bio)<p class="text-surface-600 text-sm">{{ Str::limit($member->bio, 100) }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
