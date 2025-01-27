<?php

namespace App\Http\Controllers\Web\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

class ServiceCategoryController extends Controller {
    /**
     * Display the service category page.
     *
     * @return View
     */
    public function index(): View {
        $services = Service::withCount('userServices')->where('status', 'active')->get();
        return view('frontend.layouts.service_category.index', compact('services'));
    }
}
