<?php
namespace App\Http\Controllers;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)->with('media')->orderBy('sort_order')->get();
        return view('services.index', compact('services'));
    }

    public function show(Service $service)
    {
        abort_unless($service->is_active, 404);
        $service->load('media');
        return view('services.show', compact('service'));
    }
}
