<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaLibraryItem;
use Illuminate\Http\Request;

/**
 * Lightweight upload/delete endpoints used by the in-modal Media Library picker
 * (resources/views/filament/actions/library-picker-grid.blade.php). Admin-only.
 */
class MediaLibraryController extends Controller
{
    public function upload(Request $request)
    {
        abort_unless($request->user()?->can('media_library.create'), 403);

        $request->validate([
            'file' => ['required', 'image', 'max:10240'], // 10 MB
        ]);

        $file = $request->file('file');
        $hash = md5_file($file->getRealPath());

        // Dedup — return the existing item if the same file is already in the library
        if ($existing = MediaLibraryItem::where('hash', $hash)->first()) {
            return response()->json($this->itemJson($existing));
        }

        $item = MediaLibraryItem::create([
            'title'       => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) ?: 'Untitled',
            'hash'        => $hash,
            'uploaded_by' => $request->user()->id,
        ]);

        $item->addMedia($file)->toMediaCollection('library', 'public');

        return response()->json($this->itemJson($item->fresh()));
    }

    public function update(Request $request, MediaLibraryItem $item)
    {
        abort_unless($request->user()?->can('media_library.update'), 403);

        $data = $request->validate([
            'title'       => ['nullable', 'string', 'max:255'],
            'alt_text'    => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'tags'        => ['nullable', 'string', 'max:500'], // comma-separated
        ]);

        $tags = collect(explode(',', $data['tags'] ?? ''))
            ->map(fn ($t) => trim($t))->filter()->unique()->values()->all();

        $item->update([
            'title'       => $data['title'] ?: $item->title,
            'alt_text'    => $data['alt_text'] ?? null,
            'description' => $data['description'] ?? null,
            'tags'        => $tags,
        ]);

        return response()->json($this->itemJson($item->fresh()));
    }

    public function destroy(Request $request, MediaLibraryItem $item)
    {
        abort_unless($request->user()?->can('media_library.delete'), 403);

        $item->delete(); // cascades the underlying media

        return response()->json(['ok' => true]);
    }

    protected function itemJson(MediaLibraryItem $item): array
    {
        $bytes = $item->fileSize();

        return [
            'id'          => $item->id,
            'title'       => $item->title ?: 'Untitled',
            'alt_text'    => $item->alt_text ?: '',
            'description' => $item->description ?: '',
            'thumb'       => $item->thumbUrl() ?: $item->url(),
            'tags'        => $item->tags ?: [],
            'file'        => $item->fileName(),
            'size'        => $bytes < 1024 * 1024
                ? round($bytes / 1024, 1) . ' KB'
                : round($bytes / 1024 / 1024, 2) . ' MB',
        ];
    }
}
