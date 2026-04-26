<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryItem extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['title', 'alt_text', 'tags', 'description', 'uploaded_by'];

    protected function casts(): array
    {
        return ['tags' => 'array'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('library')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(10)
            ->nonOptimized()
            ->quality(80);

        $this->addMediaConversion('medium')
            ->width(800)
            ->height(800)
            ->sharpen(10)
            ->nonOptimized()
            ->quality(85);
    }

    public function url(string $conversion = ''): string
    {
        return $this->getFirstMediaUrl('library', $conversion);
    }

    public function thumbUrl(): string
    {
        return $this->getFirstMediaUrl('library', 'thumb') ?: $this->url();
    }

    public function isImage(): bool
    {
        $mime = $this->getFirstMedia('library')?->mime_type ?? '';
        return str_starts_with($mime, 'image/');
    }

    public function fileSize(): int
    {
        return (int) ($this->getFirstMedia('library')?->size ?? 0);
    }

    public function fileName(): ?string
    {
        return $this->getFirstMedia('library')?->file_name;
    }
}
