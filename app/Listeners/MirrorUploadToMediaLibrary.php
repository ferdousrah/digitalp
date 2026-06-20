<?php

namespace App\Listeners;

use App\Models\MediaLibraryItem;
use App\Models\Product;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

/**
 * Auto-mirror images uploaded directly to a product into the reusable Media Library,
 * so admins can pick them again later instead of re-uploading. Deduped by content hash;
 * files attached FROM the library (carrying the `from_library` custom property) are skipped
 * to avoid a round-trip loop.
 */
class MirrorUploadToMediaLibrary
{
    /** Product collections whose uploads get mirrored into the Media Library. */
    protected array $mirrorCollections = ['product_thumbnail', 'product_images'];

    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;

        // The library's own uploads: stamp a content hash for dedup, then stop (no mirror, no loop).
        if ($media->model_type === MediaLibraryItem::class) {
            if ($media->collection_name === 'library' && ($item = $media->model) && blank($item->hash)) {
                $item->forceFill(['hash' => $this->hashOf($media)])->saveQuietly();
            }
            return;
        }

        // Only mirror product image uploads — and never files that came from the library.
        if ($media->model_type !== Product::class
            || ! in_array($media->collection_name, $this->mirrorCollections, true)
            || $media->getCustomProperty('from_library')) {
            return;
        }

        $hash = $this->hashOf($media);
        if (! $hash || MediaLibraryItem::where('hash', $hash)->exists()) {
            return; // identical file already in the library
        }

        $item = MediaLibraryItem::create([
            'title'       => pathinfo($media->file_name, PATHINFO_FILENAME) ?: 'Untitled',
            'hash'        => $hash,
            'uploaded_by' => auth()->id(),
        ]);

        $item->addMediaFromDisk($media->getPathRelativeToRoot(), $media->disk)
            ->preservingOriginal()
            ->usingName($media->name)
            ->usingFileName($media->file_name)
            ->toMediaCollection('library', 'public');
    }

    protected function hashOf($media): ?string
    {
        try {
            $path = $media->getPath();
            return is_file($path) ? md5_file($path) : null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
