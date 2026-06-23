<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Services\SeoService;

class OfferController extends Controller
{
    public function index()
    {
        $offers = Offer::visible()->with('media')->get();

        app(SeoService::class)
            ->title('Offers & Promotions')
            ->description('Latest deals, festivals and special offers — grab them before they end.');

        return view('offers.index', compact('offers'));
    }

    public function show(Offer $offer)
    {
        abort_unless($offer->is_active, 404);
        $offer->load([
            'media',
            'products' => fn ($q) => $q->where('is_active', true)->with('media', 'brand'),
        ]);

        $more = Offer::visible()->where('id', '!=', $offer->id)->with('media')->take(3)->get();

        app(SeoService::class)
            ->title($offer->meta_title ?: $offer->title)
            ->description($offer->meta_description ?: ($offer->subtitle ?: \Illuminate\Support\Str::limit(strip_tags((string) $offer->body), 160)))
            ->image($offer->getFirstMediaUrl('offer_banner', 'large') ?: $offer->getFirstMediaUrl('offer_banner'))
            ->canonical(route('offers.show', $offer));

        return view('offers.show', compact('offer', 'more'));
    }
}
