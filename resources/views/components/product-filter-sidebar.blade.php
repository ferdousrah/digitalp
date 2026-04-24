@props([
    'action' => route('products.index'),
    'categories' => null,
    'brands' => collect(),
    'filterAttributes' => [],
    'priceRange' => ['min' => 0, 'max' => 0],
])

@php
    $activeAttrs = request()->input('attr', []);
@endphp

<aside class="lg:w-72 flex-shrink-0">
    <form id="filter-form" method="GET" action="{{ $action }}">
        <div class="bg-white rounded-xl border border-surface-200 sticky top-24 overflow-hidden">
            <!-- Header -->
            <div class="flex justify-between items-center px-5 py-4 border-b border-surface-100" style="background: linear-gradient(135deg, #f0fdf4, #fff);">
                <h3 class="font-semibold text-lg text-surface-900">Filter By</h3>
                <a href="{{ $action }}" data-filter-reset class="text-sm text-accent-500 hover:text-accent-600 font-medium">Reset</a>
            </div>

            <div class="divide-y divide-surface-100" style="max-height: calc(100vh - 140px); overflow-y: auto;">

                {{-- Category (only on products.index) --}}
                @if($categories)
                <div class="px-5 py-4">
                    <h4 class="font-medium text-surface-900 mb-3 text-sm">Category</h4>
                    <select name="category" class="w-full border-surface-300 rounded-lg text-sm" data-filter-input>
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->products_count }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Price Range --}}
                <div class="px-5 py-4" x-data="{
                    min: {{ (int) request('min_price', $priceRange['min']) }},
                    max: {{ (int) request('max_price', $priceRange['max']) }},
                    rangeMin: {{ (int) $priceRange['min'] }},
                    rangeMax: {{ (int) $priceRange['max'] }},
                    open: true,
                    pct(val) { return this.rangeMax === this.rangeMin ? 0 : ((val - this.rangeMin) / (this.rangeMax - this.rangeMin)) * 100; },
                    clampMin() { if (Number(this.min) >= Number(this.max)) this.min = Number(this.max) - 1; if (this.min < this.rangeMin) this.min = this.rangeMin; },
                    clampMax() { if (Number(this.max) <= Number(this.min)) this.max = Number(this.min) + 1; if (this.max > this.rangeMax) this.max = this.rangeMax; }
                }">
                    <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <h4 class="font-medium text-surface-900 text-sm">Price</h4>
                        <svg :class="open && 'rotate-180'" class="w-4 h-4 text-surface-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3">
                        <div style="display:flex; gap:8px; margin-bottom:12px;">
                            <input type="number" name="min_price" x-model="min" :placeholder="rangeMin" @input="clampMin()" style="width:100%; border:1px solid #d1d5db; border-radius:8px; font-size:0.875rem; padding:6px 10px;" data-filter-input data-debounce>
                            <input type="number" name="max_price" x-model="max" :placeholder="rangeMax" @input="clampMax()" style="width:100%; border:1px solid #d1d5db; border-radius:8px; font-size:0.875rem; padding:6px 10px;" data-filter-input data-debounce>
                        </div>
                        {{-- Dual range slider --}}
                        <div style="position:relative; height:28px; width:100%;">
                            {{-- Track background --}}
                            <div style="position:absolute; top:50%; left:0; right:0; height:5px; transform:translateY(-50%); background:#e5e7eb; border-radius:3px;"></div>
                            {{-- Active track --}}
                            <div style="position:absolute; top:50%; height:5px; transform:translateY(-50%); background:#16a34a; border-radius:3px;" :style="'left:' + pct(min) + '%; right:' + (100 - pct(max)) + '%'"></div>
                            {{-- Min slider --}}
                            <input type="range" :min="rangeMin" :max="rangeMax" x-model="min" @input="clampMin()" data-filter-input data-debounce
                                style="position:absolute; top:0; left:0; width:100%; height:100%; margin:0; -webkit-appearance:none; appearance:none; background:transparent; pointer-events:none; z-index:3;"
                                class="dual-range-thumb">
                            {{-- Max slider --}}
                            <input type="range" :min="rangeMin" :max="rangeMax" x-model="max" @input="clampMax()" data-filter-input data-debounce
                                style="position:absolute; top:0; left:0; width:100%; height:100%; margin:0; -webkit-appearance:none; appearance:none; background:transparent; pointer-events:none; z-index:4;"
                                class="dual-range-thumb">
                        </div>
                        <label style="display:flex; align-items:center; gap:8px; margin-top:12px;">
                            <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} class="rounded border-surface-300 text-primary-600" data-filter-input>
                            <span style="font-size:0.875rem; color:#374151;">Exclude Out of Stock Items</span>
                        </label>
                    </div>
                </div>
                <style>
                    .dual-range-thumb::-webkit-slider-thumb {
                        -webkit-appearance: none; appearance: none;
                        width: 18px; height: 18px; border-radius: 50%;
                        background: #16a34a; border: 2px solid #fff;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
                        cursor: pointer; pointer-events: auto;
                    }
                    .dual-range-thumb::-moz-range-thumb {
                        width: 18px; height: 18px; border-radius: 50%;
                        background: #16a34a; border: 2px solid #fff;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
                        cursor: pointer; pointer-events: auto;
                    }
                </style>

                {{-- Brand --}}
                @if($brands->count())
                <div class="px-5 py-4" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <h4 class="font-medium text-surface-900 text-sm">Brand</h4>
                        <svg class="w-4 h-4 text-surface-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 space-y-2" style="max-height: 200px; overflow-y: auto;">
                        @php $selectedBrands = (array) request('brand', []); @endphp
                        @foreach($brands as $brand)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="brand[]" value="{{ $brand->slug }}"
                                {{ in_array($brand->slug, $selectedBrands) ? 'checked' : '' }}
                                class="rounded border-surface-300 text-primary-600"
                                data-filter-input>
                            <span class="text-sm text-surface-700 group-hover:text-primary-600 flex-1">{{ $brand->name }}</span>
                            <span class="text-xs text-surface-400">{{ $brand->products_count }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Dynamic Attribute Filters --}}
                @foreach($filterAttributes as $attr)
                <div class="px-5 py-4" x-data="{ open: false }">
                    <button type="button" @click="open = !open" class="flex justify-between items-center w-full text-left">
                        <h4 class="font-medium text-surface-900 text-sm">{{ $attr['name'] }}</h4>
                        <span class="flex items-center gap-1">
                            @if(!empty($activeAttrs[$attr['slug']]))
                                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                            @endif
                            <svg class="w-4 h-4 text-surface-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>
                    <div x-show="open" x-collapse class="mt-3 space-y-2" style="max-height: 200px; overflow-y: auto;">
                        @php
                            $selectedValues = [];
                            if (!empty($activeAttrs[$attr['slug']])) {
                                $selectedValues = is_array($activeAttrs[$attr['slug']])
                                    ? $activeAttrs[$attr['slug']]
                                    : explode(',', $activeAttrs[$attr['slug']]);
                            }
                        @endphp
                        @foreach($attr['options'] as $option)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox"
                                name="attr[{{ $attr['slug'] }}][]"
                                value="{{ $option['value'] }}"
                                {{ in_array($option['value'], $selectedValues) ? 'checked' : '' }}
                                class="rounded border-surface-300 text-primary-600"
                                data-filter-input>
                            <span class="text-sm text-surface-700 group-hover:text-primary-600 flex-1">{{ $option['value'] }}</span>
                            <span class="text-xs text-surface-400">{{ $option['count'] }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        {{-- Preserve sort --}}
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
    </form>
</aside>
