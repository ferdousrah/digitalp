<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\SettingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Google Merchant Center product feed (RSS 2.0 + the g: namespace).
 * Submit the URL — https://<site>/feed/google-merchant.xml — as a "Scheduled fetch"
 * feed in Merchant Center to get free Google Shopping listings.
 *
 * Currency is BDT. Products are listed without GTIN/MPN (identifier_exists = no), which
 * is valid for store/own-brand goods that don't carry a barcode.
 */
class MerchantFeedController extends Controller
{
    public function feed()
    {
        // Reuse the sitemap version stamp so a product save invalidates this feed too.
        $version = (int) Cache::get('sitemap.version', 1);

        $xml = Cache::remember("merchant-feed.v{$version}", now()->addHours(6), function () {
            return $this->build();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    protected function build(): string
    {
        $base      = rtrim(config('app.url'), '/');
        $siteName  = SettingService::get('site_name', config('app.name'));
        $siteDesc  = SettingService::get('site_description', $siteName . ' — online store');

        // Category id → "Parent > Child > Sub-child" path, built once (no N+1).
        $catMap = Category::query()->get(['id', 'name', 'parent_id'])->keyBy('id');
        $pathOf = function (?int $catId) use ($catMap): string {
            $parts = [];
            $node  = $catId ? $catMap->get($catId) : null;
            $guard = 0;
            while ($node && $guard++ < 6) {
                array_unshift($parts, $node->name);
                $node = $node->parent_id ? $catMap->get($node->parent_id) : null;
            }
            return implode(' > ', $parts);
        };

        $items = '';

        Product::query()
            ->where('is_active', true)
            ->with([
                'brand:id,name',
                'media',
                'categories:id,name,parent_id',
            ])
            ->orderBy('id')
            ->chunk(200, function ($products) use (&$items, $base, $pathOf) {
                foreach ($products as $p) {
                    $item = $this->renderItem($p, $base, $pathOf);
                    if ($item !== null) {
                        $items .= $item;
                    }
                }
            });

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= "    <title>" . $this->esc($siteName) . "</title>\n";
        $xml .= "    <link>" . $this->esc($base) . "</link>\n";
        $xml .= "    <description>" . $this->esc(Str::limit(strip_tags((string) $siteDesc), 300)) . "</description>\n";
        $xml .= $items;
        $xml .= "  </channel>\n";
        $xml .= '</rss>' . "\n";

        return $xml;
    }

    /** Render one <item>, or null if the product can't be listed (e.g. no image). */
    protected function renderItem(Product $p, string $base, \Closure $pathOf): ?string
    {
        // Image is required by Google — skip products without one.
        $image = $p->getFirstMediaUrl('product_thumbnail', 'large')
            ?: $p->getFirstMediaUrl('product_images', 'large');
        if (! $image) {
            return null;
        }

        $additional = [];
        foreach ($p->getMedia('product_images') as $m) {
            $url = $m->getUrl('large');
            if ($url && $url !== $image && count($additional) < 10) {
                $additional[] = $url;
            }
        }

        // Pricing: compare_price (when higher) is the "was" price → regular price,
        // and the current price becomes the sale price.
        $regular = (float) $p->price;
        $sale    = null;
        if ($p->compare_price && (float) $p->compare_price > (float) $p->price) {
            $regular = (float) $p->compare_price;
            $sale    = (float) $p->price;
        }

        $availability = ($p->in_stock && (int) $p->stock_quantity > 0) ? 'in_stock' : 'out_of_stock';

        $description = trim(preg_replace('/\s+/', ' ', strip_tags((string) ($p->short_description ?: $p->description))));
        if ($description === '') {
            $description = $p->name;
        }

        $firstCatId = optional($p->categories->first())->id;
        $productType = $pathOf($firstCatId);

        $x  = "    <item>\n";
        $x .= $this->tag('g:id', $p->sku ?: ('product-' . $p->id));
        $x .= $this->tag('g:title', Str::limit($p->name, 150, ''));
        $x .= $this->tag('g:description', Str::limit($description, 5000, ''));
        $x .= $this->tag('g:link', $base . '/products/' . $p->slug);
        $x .= $this->tag('g:image_link', $image);
        foreach ($additional as $url) {
            $x .= $this->tag('g:additional_image_link', $url);
        }
        $x .= $this->tag('g:availability', $availability);
        $x .= $this->tag('g:price', number_format($regular, 2, '.', '') . ' BDT');
        if ($sale !== null) {
            $x .= $this->tag('g:sale_price', number_format($sale, 2, '.', '') . ' BDT');
        }
        $x .= $this->tag('g:condition', 'new');
        if ($p->brand) {
            $x .= $this->tag('g:brand', $p->brand->name);
        }
        // No barcodes on these products — tell Google explicitly so it doesn't flag them.
        $x .= $this->tag('g:identifier_exists', 'no');
        if ($productType !== '') {
            $x .= $this->tag('g:product_type', $productType);
        }
        $x .= "    </item>\n";

        return $x;
    }

    protected function tag(string $name, string $value): string
    {
        return "      <{$name}>" . $this->esc($value) . "</{$name}>\n";
    }

    protected function esc(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
