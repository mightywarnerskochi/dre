<?php

namespace App\Services;

class PropertyFinderListingSyncService
{
    private const SYNC_START_PAGE = 1;

    private const SYNC_PER_PAGE = 100;

    private const REGULAR_SYNC_PAGE_COUNT = 5;

    private const FULL_SYNC_PAGE_COUNT = 0;

    public function __construct(
        private readonly PropertyFinderApiService $propertyFinder,
    ) {}

    public function fullSync(): array
    {
        $startPage = self::SYNC_START_PAGE;
        $perPage = self::SYNC_PER_PAGE;
        $pageCount = self::FULL_SYNC_PAGE_COUNT;

        $response = $this->propertyFinder->allListings($this->listingQuery($startPage), $perPage, $pageCount);

        return $this->summary('full', $response, $startPage, $perPage, $pageCount);
    }

    public function regularSync(): array
    {
        $startPage = self::SYNC_START_PAGE;
        $perPage = self::SYNC_PER_PAGE;
        $pageCount = self::REGULAR_SYNC_PAGE_COUNT;

        $response = $this->propertyFinder->allListings($this->listingQuery($startPage), $perPage, $pageCount);

        return $this->summary('regular', $response, $startPage, $perPage, $pageCount);
    }

    private function listingQuery(int $startPage): array
    {
        return [
            'draft' => true,
            'page' => max($startPage, 1),
            'orderBy' => 'createdAt',
            'sort[createdAt]' => 'desc',
        ];
    }

    private function summary(string $type, array $response, int $startPage, int $perPage, int $pageCount): array
    {
        $results = data_get($response, 'results', []);

        return [
            'status' => true,
            'type' => $type,
            'startPage' => $startPage,
            'perPage' => $perPage,
            'pageCount' => $pageCount,
            'fetched' => count($results),
            'pagesFetched' => data_get($response, 'pagesFetched', 0),
            'pagination' => data_get($response, 'pagination', []),
            'synced' => 0,
            'skipped' => count($results),
            'message' => count($results) === 0
                ? 'No listings returned by Property Finder for this API key.'
                : 'Listings fetched. Mapping to local properties is ready to be added.',
        ];
    }
}
