@extends('layouts.app')
@section('title', 'Page Not Found - Digital Support')

@section('content')
<div class="container-custom px-4 sm:px-6 lg:px-8 py-32 text-center">
    <h1 class="text-6xl font-display text-primary-600 mb-4">404</h1>
    <p class="text-xl text-surface-600 mb-8">The page you're looking for doesn't exist.</p>
    <a href="{{ route('home') }}" class="btn-primary">Go Home</a>
</div>
@endsection
