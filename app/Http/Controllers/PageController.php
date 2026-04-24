<?php
namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\CompanyTimeline;
use App\Models\TeamMember;

class PageController extends Controller
{
    public function about()
    {
        $page = Page::where('slug', 'about')->where('is_active', true)->first();
        $timeline = CompanyTimeline::where('is_active', true)->with('media')->orderBy('sort_order')->get();
        $team = TeamMember::where('is_active', true)->with('media')->orderBy('sort_order')->get();

        return view('pages.about', compact('page', 'timeline', 'team'));
    }

    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);
        return view('pages.show', compact('page'));
    }
}
