@extends('layouts.app')
@section('title', 'FAQ - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'FAQ']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-display mb-8 text-center">Frequently Asked Questions</h1>

        @foreach($categories as $category)
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-surface-900 mb-4">{{ $category->name }}</h2>
            <div class="space-y-3" x-data="{ open: null }">
                @foreach($category->faqs as $faq)
                <div class="bg-white border border-surface-200 rounded-lg overflow-hidden">
                    <button @click="open = open === {{ $faq->id }} ? null : {{ $faq->id }}" class="w-full flex justify-between items-center p-4 text-left font-medium text-surface-900 hover:bg-surface-50 transition-colors">
                        <span>{{ $faq->question }}</span>
                        <svg class="w-5 h-5 text-surface-500 transition-transform" :class="{ 'rotate-180': open === {{ $faq->id }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open === {{ $faq->id }}" x-collapse>
                        <div class="px-4 pb-4 text-surface-600">{!! $faq->answer !!}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
