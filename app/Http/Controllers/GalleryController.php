<?php
namespace App\Http\Controllers;

use App\Models\GalleryAlbum;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::where('is_active', true)->with('media')->withCount('items')->orderBy('sort_order')->get();
        return view('gallery.index', compact('albums'));
    }

    public function show(GalleryAlbum $galleryAlbum)
    {
        abort_unless($galleryAlbum->is_active, 404);
        $galleryAlbum->load(['items' => fn($q) => $q->where('is_active', true)->with('media')->orderBy('sort_order'), 'media']);
        return view('gallery.show', ['album' => $galleryAlbum]);
    }
}
