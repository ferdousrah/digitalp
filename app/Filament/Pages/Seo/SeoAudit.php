<?php

namespace App\Filament\Pages\Seo;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Filament\Pages\Page as FilamentPage;

class SeoAudit extends FilamentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $navigationLabel = 'SEO Audit';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'SEO Audit';
    protected static ?int $navigationSort = 8;
    protected static string $routePath = 'seo/audit';
    protected static ?string $slug = 'seo/audit';
    protected static string $view = 'filament.pages.seo.audit';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('settings.view'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getSubheading(): ?string
    {
        return 'Records that are hurting your search visibility — fix these first.';
    }

    public function getIssues(): array
    {
        $issues = [
            'missing_title'       => [],
            'missing_description' => [],
            'short_title'         => [],
            'long_title'          => [],
            'short_description'   => [],
            'long_description'    => [],
            'weak_slug'           => [],
        ];

        $check = function ($model, $type, $editUrl, $titleField = 'name') use (&$issues) {
            $records = $model::query()->select(['id', $titleField, 'slug', 'meta_title', 'meta_description'])->get();

            foreach ($records as $r) {
                $url = $editUrl($r);

                $title = trim((string) $r->meta_title);
                $desc  = trim((string) $r->meta_description);

                if ($title === '')        $issues['missing_title'][]       = compact('r', 'type', 'url') + ['name' => $r->$titleField];
                if ($desc === '')         $issues['missing_description'][] = compact('r', 'type', 'url') + ['name' => $r->$titleField];
                if ($title !== '' && mb_strlen($title) < 30) $issues['short_title'][] = compact('r', 'type', 'url') + ['name' => $r->$titleField, 'len' => mb_strlen($title)];
                if (mb_strlen($title) > 60)                  $issues['long_title'][]  = compact('r', 'type', 'url') + ['name' => $r->$titleField, 'len' => mb_strlen($title)];
                if ($desc !== ''  && mb_strlen($desc) < 80)  $issues['short_description'][] = compact('r', 'type', 'url') + ['name' => $r->$titleField, 'len' => mb_strlen($desc)];
                if (mb_strlen($desc) > 170)                  $issues['long_description'][]  = compact('r', 'type', 'url') + ['name' => $r->$titleField, 'len' => mb_strlen($desc)];

                if ($r->slug && (mb_strlen($r->slug) < 3 || mb_strlen($r->slug) > 80 || ctype_digit(str_replace('-', '', $r->slug)))) {
                    $issues['weak_slug'][] = compact('r', 'type', 'url') + ['name' => $r->$titleField, 'slug' => $r->slug];
                }
            }
        };

        $check(Product::class,  'Product',  fn ($r) => url('/admin/products/' . $r->id . '/edit'));
        $check(Category::class, 'Category', fn ($r) => url('/admin/categories/' . $r->id . '/edit'));
        $check(BlogPost::class, 'Blog Post',fn ($r) => url('/admin/blog-posts/' . $r->id . '/edit'), 'title');
        $check(Page::class,     'Page',     fn ($r) => url('/admin/pages/' . $r->id . '/edit'),     'title');

        return $issues;
    }

    public function getSummary(): array
    {
        $issues = $this->getIssues();
        $total = collect($issues)->sum(fn ($g) => count($g));

        return [
            'total'     => $total,
            'critical'  => count($issues['missing_title']) + count($issues['missing_description']),
            'warnings'  => count($issues['short_title']) + count($issues['long_title']) + count($issues['short_description']) + count($issues['long_description']),
            'slug'      => count($issues['weak_slug']),
        ];
    }
}
