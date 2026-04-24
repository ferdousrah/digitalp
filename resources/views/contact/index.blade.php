@extends('layouts.app')
@section('title', 'Contact Us - Digital Support')

@section('content')
@include('components.breadcrumb', ['items' => [['label' => 'Contact']]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-display mb-4">Contact Us</h1>
            <p class="text-surface-600">Have a question? We would love to hear from you.</p>
        </div>

        @if(session('success'))
        <div class="bg-primary-50 text-primary-700 border border-primary-200 rounded-lg px-6 py-4 mb-8">{{ session('success') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-surface-700 mb-1">Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="{{ sc('contact', 'name_placeholder', 'Your full name') }}" class="w-full border-surface-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('name')<p class="text-accent-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-surface-700 mb-1">Email *</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="{{ sc('contact', 'email_placeholder', 'your@email.com') }}" class="w-full border-surface-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                            @error('email')<p class="text-accent-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-surface-700 mb-1">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ sc('contact', 'phone_placeholder', '01XXXXXXXXX') }}" class="w-full border-surface-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-surface-700 mb-1">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="{{ sc('contact', 'subject_placeholder', 'How can we help?') }}" class="w-full border-surface-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    <div>
                        <label for="message" class="block text-sm font-medium text-surface-700 mb-1">Message *</label>
                        <textarea id="message" name="message" rows="6" required placeholder="{{ sc('contact', 'message_placeholder', 'Write your message here...') }}" class="w-full border-surface-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('message') }}</textarea>
                        @error('message')<p class="text-accent-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary">Send Message</button>
                </form>
            </div>
            <div class="space-y-6">
                <div class="bg-surface-50 rounded-xl p-6">
                    <h3 class="font-semibold mb-2">Email</h3>
                    <p class="text-surface-600 text-sm">info@digitalsupport.com</p>
                </div>
                <div class="bg-surface-50 rounded-xl p-6">
                    <h3 class="font-semibold mb-2">Phone</h3>
                    <p class="text-surface-600 text-sm">+1 234 567 890</p>
                </div>
                <div class="bg-surface-50 rounded-xl p-6">
                    <h3 class="font-semibold mb-2">Address</h3>
                    <p class="text-surface-600 text-sm">123 Tech Street, Digital City, DC 12345</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
