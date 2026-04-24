@props(['items' => []])

<nav aria-label="Breadcrumb" class="container-custom px-4 sm:px-6 lg:px-8 py-4">
    <ol class="flex items-center gap-2 text-sm text-surface-500">
        <li><a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">Home</a></li>
        @foreach($items as $item)
            @if(!empty($item) && isset($item['label']))
            <li class="flex items-center gap-2">
                <svg class="w-4 h-4 text-surface-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                @if(isset($item['url']) && $item['url'])
                    <a href="{{ $item['url'] }}" class="hover:text-primary-600 transition-colors">{{ $item['label'] }}</a>
                @else
                    <span class="text-surface-900 font-medium">{{ $item['label'] }}</span>
                @endif
            </li>
            @endif
        @endforeach
    </ol>
</nav>
