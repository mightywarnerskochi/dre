<?php

namespace App\Http\Controllers\CmsKit;

use CMS\SiteManager\Services\SitemapService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $exists = file_exists(public_path('sitemap.xml'));
        return view('cms-kit::sitemap.index', compact('exists'));
    }

    public function generate(Request $request, SitemapService $sitemapService)
    {
        try {
            $sitemapService->generate();
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->with('error', 'Sitemap generation failed: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Sitemap generated successfully.');
    }

    public function edit()
    {
        $path = public_path('sitemap.xml');
        $content = file_exists($path) ? file_get_contents($path) : '';
        return view('cms-kit::sitemap.edit', compact('content'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $path = public_path('sitemap.xml');
        file_put_contents($path, $request->input('content'));

        return redirect()->route('cms.sitemap.index')->with('success', 'Sitemap.xml updated successfully.');
    }
}
