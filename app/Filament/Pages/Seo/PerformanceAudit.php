<?php

namespace App\Filament\Pages\Seo;

use App\Models\Product;
use Filament\Pages\Page as FilamentPage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PerformanceAudit extends FilamentPage
{
    protected static ?string $navigationIcon = 'heroicon-o-bolt';
    protected static ?string $navigationLabel = 'Performance Audit';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'Performance Audit';
    protected static ?int $navigationSort = 9;
    protected static string $routePath = 'seo/performance';
    protected static ?string $slug = 'seo/performance';
    protected static string $view = 'filament.pages.seo.performance-audit';

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
        return 'Spot heavy images, missing alt text, and items that hurt page-speed.';
    }

    public function getReport(): array
    {
        $LARGE_IMAGE = 500 * 1024; // 500 KB

        // All product images
        $allMedia = Media::whereIn('collection_name', ['product_thumbnail', 'product_images'])
            ->select(['id', 'name', 'file_name', 'size', 'collection_name', 'model_id', 'mime_type', 'generated_conversions'])
            ->get();

        $totalCount = $allMedia->count();
        $totalSize  = (int) $allMedia->sum('size');

        $large = $allMedia->filter(fn ($m) => (int) $m->size > $LARGE_IMAGE)
            ->sortByDesc('size')
            ->take(50)
            ->map(function ($m) {
                $product = Product::find($m->model_id);
                return [
                    'id'         => $m->id,
                    'product'    => $product?->name ?? '—',
                    'product_id' => $m->model_id,
                    'file'       => $m->file_name,
                    'size'       => (int) $m->size,
                    'mime'       => $m->mime_type,
                ];
            })->values()->all();

        // Conversions audit — how many media records have a webp variant generated?
        $webpReady = 0;
        $webpMissing = 0;
        foreach ($allMedia as $m) {
            $conv = is_string($m->generated_conversions)
                ? json_decode($m->generated_conversions, true)
                : (array) $m->generated_conversions;
            $hasWebp = isset($conv['medium_webp']) && $conv['medium_webp'];
            $hasWebp ? $webpReady++ : $webpMissing++;
        }

        // Products without alt-friendly names
        $shortNamed = Product::where('is_active', true)
            ->where(function ($q) { $q->whereRaw('LENGTH(name) < 10')->orWhereNull('name'); })
            ->limit(50)->get(['id', 'name']);

        return [
            'total_count'   => $totalCount,
            'total_size'    => $totalSize,
            'avg_size'      => $totalCount > 0 ? (int) round($totalSize / $totalCount) : 0,
            'large_count'   => count($large),
            'large_images'  => $large,
            'webp_ready'    => $webpReady,
            'webp_missing'  => $webpMissing,
            'short_named'   => $shortNamed,
        ];
    }
}
