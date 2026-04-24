<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Faq;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Services\SettingService;

class HomeController extends Controller
{
    public function index()
    {
        $activeSliderQuery = fn($q) => $q->where('is_active', true)
            ->where(function ($q) { $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()); })
            ->where(function ($q) { $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()); })
            ->orderBy('sort_order')
            ->with('media');

        $sliders = (clone $activeSliderQuery(Slider::query()))->where('position', 'home_hero')->get();
        $banner1 = (clone $activeSliderQuery(Slider::query()))->where('position', 'home_banner_1')->first();
        $banner2 = (clone $activeSliderQuery(Slider::query()))->where('position', 'home_banner_2')->first();

        $heroSliderHeight   = (int)  SettingService::get('hero_slider_height',    420);
        $heroSliderColWidth = (int)  SettingService::get('hero_slider_col_width', 75);
        $heroBannerColWidth = (int)  SettingService::get('hero_banner_col_width', 25);
        $heroShowBanners    = (bool) SettingService::get('hero_show_banners', '1');

        // Load enabled sections ordered
        $sections = HomeSection::where('is_enabled', true)->orderBy('sort_order')->get();

        // Load data for each section type
        $sectionData = [];
        foreach ($sections as $section) {
            $sectionData[$section->key] = $this->loadSectionData($section);
        }

        return view('home.index', compact(
            'sliders', 'banner1', 'banner2',
            'heroSliderHeight', 'heroSliderColWidth', 'heroBannerColWidth', 'heroShowBanners',
            'sections', 'sectionData'
        ));
    }

    private function loadSectionData(HomeSection $section): mixed
    {
        return match($section->type) {
            'category' => Category::active()->whereNull('parent_id')
                ->withCount('products')->with('media')
                ->orderBy('sort_order')->limit($section->items_count)->get(),

            'product' => $this->loadProducts($section),

            'offer_banner' => Slider::where('is_active', true)
                ->where('position', $section->extra['banner_position'] ?? 'home_offer')
                ->with('media')->orderBy('sort_order')->get(),

            'reviews' => Testimonial::where('is_active', true)
                ->with('media')->orderBy('sort_order')->limit($section->items_count)->get(),

            'blog' => \App\Models\BlogPost::where('status', 'published')
                ->orderBy('published_at', 'desc')->limit($section->items_count)->get(),

            'brands' => Brand::where('is_active', true)
                ->with('media')->orderBy('sort_order')->get(),

            'faq' => Faq::active()->with('category')->orderBy('sort_order')->get(),

            default => null,
        };
    }

    private function loadProducts(HomeSection $section): \Illuminate\Database\Eloquent\Collection
    {
        $filter = $section->extra['product_filter'] ?? 'featured';
        $query  = Product::active()->with(['media', 'brand', 'categories'])->limit($section->items_count);

        return match($filter) {
            'featured'    => $query->where('is_featured', true)->orderBy('sort_order')->get(),
            'new_arrival' => $query->where('is_new_arrival', true)->orderByDesc('created_at')->get(),
            'best_seller' => $query->where('is_best_seller', true)->orderBy('sort_order')->get(),
            'category'    => $query->whereHas('categories', fn($q) => $q->where('categories.id', $section->extra['category_id'] ?? 0))->orderBy('sort_order')->get(),
            default       => $query->where('is_featured', true)->orderBy('sort_order')->get(),
        };
    }
}
