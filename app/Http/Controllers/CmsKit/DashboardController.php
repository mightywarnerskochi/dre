<?php

namespace App\Http\Controllers\CmsKit;

use App\Models\CmsKit\Blog;
use App\Models\CmsKit\Career;
use App\Models\CmsKit\Enquiry;
use App\Models\CmsKit\Faq;
use App\Models\CmsKit\Property;
use App\Models\CmsKit\Testimonial;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'faqs' => Faq::count(),
            'enquiries' => Enquiry::count(),
            'testimonials' => Testimonial::count(),
            'careers' => class_exists(Career::class) ? Career::count() : 0,
            'properties' => class_exists(Property::class) ? Property::count() : 0,
            'blogs' => class_exists(Blog::class) ? Blog::count() : 0,
        ];

        $recentEnquiries = Enquiry::latest()->take(8)->get();

        return view('cms-kit::dashboard', compact('stats', 'recentEnquiries'));
    }
}
