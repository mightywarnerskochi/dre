<?php

namespace App\Http\Controllers;

use App\Support\SitemapCrawlLinks;
use Illuminate\Routing\Controller;

class SitemapCrawlController extends Controller
{
    public function __invoke(SitemapCrawlLinks $links)
    {
        return response()
            ->view('sitemap.crawl-links', [
                'urls' => $links->urls(),
            ])
            ->header('X-Robots-Tag', 'noindex, follow');
    }
}
