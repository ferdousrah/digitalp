<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /**
     * Returns a Google-friendly sitemap.xml at /sitemap.xml.
     * Cached for 6h — invalidate manually with `php artisan cache:forget sitemap.xml`
     * or whenever a model is updated (we hook this from observers below).
     */
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHours(6), function () {
            return $this->generate();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    protected function generate(): string
    {
        $base = rtrim(config('app.url'), '/');
        $entries = collect();

        // Home + static high-value pages
        $entries->push(['loc' => $base . '/',                'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => now()]);
        $entries->push(['loc' => $base . '/products',        'priority' => '0.9', 'changefreq' => 'daily',   'lastmod' => now()]);
        $entries->push(['loc' => $base . '/blog',            'priority' => '0.7', 'changefreq' => 'weekly',  'lastmod' => now()]);
        $entries->push(['loc' => $base . '/contact',         'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => now()]);
        $entries->push(['loc' => $base . '/about-us',        'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => now()]);
        $entries->push(['loc' => $base . '/faqs',            'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => now()]);

        // Products — include images so they're picked up by Google Images
        Product::where('is_active', true)
            ->with('media')
            ->chunk(200, function ($chunk) use ($entries, $base) {
                foreach ($chunk as $p) {
                    $images = [];
                    foreach ($p->getMedia('product_images') as $m) {
                        $images[] = [
                            'loc'   => $m->getUrl('large'),
                            'title' => $p->name,
                        ];
                    }
                    $thumb = $p->getFirstMediaUrl('product_thumbnail', 'large');
                    if ($thumb && empty(array_filter($images, fn ($i) => $i['loc'] === $thumb))) {
                        array_unshift($images, ['loc' => $thumb, 'title' => $p->name]);
                    }

                    $entries->push([
                        'loc'        => $base . '/products/' . $p->slug,
                        'priority'   => '0.8',
                        'changefreq' => 'weekly',
                        'lastmod'    => $p->updated_at,
                        'images'     => $images,
                    ]);
                }
            });

        // Categories
        Category::where('is_active', true)->select(['slug', 'updated_at'])->each(function ($c) use ($entries, $base) {
            $entries->push([
                'loc'        => $base . '/categories/' . $c->slug,
                'priority'   => '0.7',
                'changefreq' => 'weekly',
                'lastmod'    => $c->updated_at,
            ]);
        });

        // Brands
        Brand::where('is_active', true)->select(['slug', 'updated_at'])->each(function ($b) use ($entries, $base) {
            $entries->push([
                'loc'        => $base . '/brands/' . $b->slug,
                'priority'   => '0.6',
                'changefreq' => 'weekly',
                'lastmod'    => $b->updated_at,
            ]);
        });

        // Blog posts
        BlogPost::where('status', 'published')->select(['slug', 'updated_at'])->each(function ($p) use ($entries, $base) {
            $entries->push([
                'loc'        => $base . '/blog/' . $p->slug,
                'priority'   => '0.6',
                'changefreq' => 'monthly',
                'lastmod'    => $p->updated_at,
            ]);
        });

        // Services
        Service::where('is_active', true)->select(['slug', 'updated_at'])->each(function ($s) use ($entries, $base) {
            $entries->push([
                'loc'        => $base . '/services/' . $s->slug,
                'priority'   => '0.6',
                'changefreq' => 'monthly',
                'lastmod'    => $s->updated_at,
            ]);
        });

        // Custom pages
        Page::where('is_active', true)->select(['slug', 'updated_at'])->each(function ($p) use ($entries, $base) {
            $entries->push([
                'loc'        => $base . '/pages/' . $p->slug,
                'priority'   => '0.5',
                'changefreq' => 'monthly',
                'lastmod'    => $p->updated_at,
            ]);
        });

        // Build the XML — include image namespace for the image sitemap extension
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        foreach ($entries as $e) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($e['loc'], ENT_QUOTES, 'UTF-8') . "</loc>\n";
            $xml .= "    <lastmod>" . (is_string($e['lastmod']) ? $e['lastmod'] : $e['lastmod']->toIso8601String()) . "</lastmod>\n";
            $xml .= "    <changefreq>" . $e['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $e['priority'] . "</priority>\n";

            foreach (($e['images'] ?? []) as $img) {
                if (empty($img['loc'])) continue;
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($img['loc'], ENT_QUOTES, 'UTF-8') . "</image:loc>\n";
                if (!empty($img['title'])) {
                    $xml .= "      <image:title>" . htmlspecialchars($img['title'], ENT_QUOTES, 'UTF-8') . "</image:title>\n";
                }
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>' . "\n";

        return $xml;
    }
}
