<?php

namespace App\Services;

use App\Support\SitemapCrawlLinks;
use CMS\SiteManager\Services\SitemapService as BaseSitemapService;
use Psr\Http\Message\UriInterface;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SitemapService extends BaseSitemapService
{
    public function __construct(private readonly SitemapCrawlLinks $links)
    {
    }

    protected function fullCrawl(): void
    {
        $crawlUrl = route('sitemap.crawl');
        $allowedUrls = collect($this->links->urls())
            ->push($crawlUrl)
            ->map(fn ($url) => $this->normalizeUrl($url))
            ->unique()
            ->values()
            ->all();

        SitemapGenerator::create($crawlUrl)
            ->shouldCrawl(fn (UriInterface $url) => in_array($this->normalizeUrl((string) $url), $allowedUrls, true))
            ->hasCrawled(function (Url $url) use ($crawlUrl) {
                if ($this->normalizeUrl($url->url) === $this->normalizeUrl($crawlUrl)) {
                    return null;
                }

                $url->setLastModificationDate(now());
                $url->setPriority(in_array($url->path(), ['', '/'], true) ? 1.0 : 0.8);

                return $url;
            })
            ->writeToFile(public_path('sitemap.xml'));
    }

    private function normalizeUrl(string $url): string
    {
        return rtrim($url, '/') ?: $url;
    }
}
