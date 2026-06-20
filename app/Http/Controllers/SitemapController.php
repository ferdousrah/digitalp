<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    /** Max URLs per sub-sitemap (Google's limit is 50,000). */
    protected const PER_PAGE = 40000;

    /**
     * /sitemap.xml — a sitemap INDEX pointing at the type/page sub-sitemaps.
     * Scales past 50k URLs (products are paginated). Cached, version-busted on model save.
     */
    public function index()
    {
        $xml = $this->cached('index', function () {
            $base = $this->base();
            $maps = [$base . '/sitemap-core.xml'];

            $productPages = max(1, (int) ceil(Product::where('is_active', true)->count() / self::PER_PAGE));
            for ($p = 1; $p <= $productPages; $p++) {
                $maps[] = $base . '/sitemap-products-' . $p . '.xml';
            }
            $maps[] = $base . '/sitemap-blog.xml';

            $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($maps as $loc) {
                $xml .= "  <sitemap><loc>" . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . "</loc>"
                      . "<lastmod>" . now()->toIso8601String() . "</lastmod></sitemap>\n";
            }
            $xml .= '</sitemapindex>' . "\n";
            return $xml;
        });

        return $this->respond($xml);
    }

    /** Static pages + categories + services + custom pages (all low-volume). */
    public function core()
    {
        $xml = $this->cached('core', function () {
            $base = $this->base();
            $entries = collect();

            $entries->push(['loc' => $base . '/',         'priority' => '1.0', 'changefreq' => 'daily',   'lastmod' => now()]);
            $entries->push(['loc' => $base . '/products', 'priority' => '0.9', 'changefreq' => 'daily',   'lastmod' => now()]);
            $entries->push(['loc' => $base . '/blog',     'priority' => '0.7', 'changefreq' => 'weekly',  'lastmod' => now()]);
            $entries->push(['loc' => $base . '/contact',  'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => now()]);
            $entries->push(['loc' => $base . '/about',    'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => now()]);
            $entries->push(['loc' => $base . '/faq',      'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => now()]);

            Category::where('is_active', true)->select(['slug', 'updated_at'])->each(fn ($c) => $entries->push([
                'loc' => $base . '/categories/' . $c->slug, 'priority' => '0.7', 'changefreq' => 'weekly', 'lastmod' => $c->updated_at,
            ]));
            Service::where('is_active', true)->select(['slug', 'updated_at'])->each(fn ($s) => $entries->push([
                'loc' => $base . '/services/' . $s->slug, 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $s->updated_at,
            ]));
            Page::where('is_active', true)->select(['slug', 'updated_at'])->each(fn ($p) => $entries->push([
                'loc' => $base . '/page/' . $p->slug, 'priority' => '0.5', 'changefreq' => 'monthly', 'lastmod' => $p->updated_at,
            ]));

            return $this->urlset($entries->all());
        });

        return $this->respond($xml);
    }

    /** Products (page N) — includes image tags for Google Images. */
    public function products(int $page = 1)
    {
        $page = max(1, $page);
        $xml = $this->cached('products.' . $page, function () use ($page) {
            $base = $this->base();
            $entries = [];

            Product::where('is_active', true)
                ->with('media')
                ->orderBy('id')
                ->forPage($page, self::PER_PAGE)
                ->get()
                ->each(function ($p) use (&$entries, $base) {
                    $images = [];
                    foreach ($p->getMedia('product_images') as $m) {
                        $images[] = ['loc' => $m->getUrl('large'), 'title' => $p->name];
                    }
                    $thumb = $p->getFirstMediaUrl('product_thumbnail', 'large');
                    if ($thumb && empty(array_filter($images, fn ($i) => $i['loc'] === $thumb))) {
                        array_unshift($images, ['loc' => $thumb, 'title' => $p->name]);
                    }
                    $entries[] = [
                        'loc' => $base . '/products/' . $p->slug, 'priority' => '0.8',
                        'changefreq' => 'weekly', 'lastmod' => $p->updated_at, 'images' => $images,
                    ];
                });

            return $this->urlset($entries);
        });

        return $this->respond($xml);
    }

    /** Blog posts. */
    public function blog()
    {
        $xml = $this->cached('blog', function () {
            $base = $this->base();
            $entries = [];
            BlogPost::where('status', 'published')->select(['slug', 'updated_at'])->each(fn ($p) => $entries[] = [
                'loc' => $base . '/blog/' . $p->slug, 'priority' => '0.6', 'changefreq' => 'monthly', 'lastmod' => $p->updated_at,
            ]);
            return $this->urlset($entries);
        });

        return $this->respond($xml);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    protected function base(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /** Version-stamped cache so a single observer bump invalidates every sub-sitemap. */
    protected function cached(string $name, \Closure $build): string
    {
        $version = (int) Cache::get('sitemap.version', 1);
        return Cache::remember("sitemap.{$name}.v{$version}", now()->addHours(6), $build);
    }

    protected function respond(string $xml)
    {
        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /** @param array<int,array> $entries */
    protected function urlset(array $entries): string
    {
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
