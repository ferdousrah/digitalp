<?php
namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('status', 'published')
            ->where('published_at', '<=', now())
            ->with(['media', 'category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        $categories = BlogCategory::where('is_active', true)->withCount(['posts' => fn($q) => $q->where('status', 'published')])->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(BlogPost $blogPost)
    {
        abort_unless($blogPost->status === 'published', 404);
        $blogPost->load(['media', 'category', 'author']);
        $blogPost->increment('view_count');

        $relatedPosts = BlogPost::where('status', 'published')
            ->where('id', '!=', $blogPost->id)
            ->where('blog_category_id', $blogPost->blog_category_id)
            ->with('media')
            ->limit(3)
            ->get();

        return view('blog.show', compact('blogPost', 'relatedPosts'));
    }

    public function category(BlogCategory $blogCategory)
    {
        $posts = BlogPost::where('status', 'published')
            ->where('blog_category_id', $blogCategory->id)
            ->with(['media', 'category', 'author'])
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        $categories = BlogCategory::where('is_active', true)->withCount(['posts' => fn($q) => $q->where('status', 'published')])->get();

        return view('blog.index', compact('posts', 'categories', 'blogCategory'));
    }
}
