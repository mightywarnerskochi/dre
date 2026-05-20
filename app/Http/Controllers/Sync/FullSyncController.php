<?php

namespace App\Http\Controllers\Sync;

use App\Http\Controllers\Controller;
use App\Services\PropertyFinderApiService;
use App\Services\PropertyFinderListingSyncService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Throwable;

class FullSyncController extends Controller
{
    public function getData(Request $request, PropertyFinderApiService $propertyFinder)
    {
        try {
            [$page, $perPage, $maxPages] = $this->pagination($request);

            $data = $propertyFinder->allListings([
                'draft' => true,
                'page' => $page,
                'orderBy' => 'createdAt',
                'sort[createdAt]' => 'desc',
            ], perPage: $perPage, maxPages: $maxPages);

            return response()->json([
                'status' => true,
                'endpoint' => '/v1/listings',
                'data' => $data,
            ]);
        } catch (RequestException $exception) {
            return response()->json([
                'status' => false,
                'http_status' => $exception->response->status(),
                'message' => $exception->response->json() ?: $exception->response->body(),
            ], $exception->response->status());
        } catch (Throwable $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function users(Request $request, PropertyFinderApiService $propertyFinder)
    {
        [$page, $perPage, $maxPages] = $this->pagination($request);

        return $this->apiResponse('/v1/users', fn () => $propertyFinder->allUsers([
            'page' => $page,
        ], perPage: $perPage, maxPages: $maxPages));
    }

    public function locations(Request $request, PropertyFinderApiService $propertyFinder)
    {
        [$page, $perPage, $maxPages] = $this->pagination($request);

        return $this->apiResponse('/v1/locations', fn () => $propertyFinder->allLocations([
            'search' => 'Dubai',
            'page' => $page,
        ], perPage: $perPage, maxPages: $maxPages));
    }

    public function regularSync(PropertyFinderListingSyncService $syncService)
    {
        return $this->apiResponse('regular-sync', fn () => $syncService->regularSync());
    }

    public function fullSync(PropertyFinderListingSyncService $syncService)
    {
        return $this->apiResponse('full-sync', fn () => $syncService->fullSync());
    }

    private function pagination(Request $request): array
    {
        return [
            max((int) $request->query('page', 1), 1),
            min(max((int) $request->query('perPage', 10), 1), 100),
            max((int) $request->query('maxPages', 1), 0),
        ];
    }

    private function apiResponse(string $endpoint, callable $callback)
    {
        try {
            return response()->json([
                'status' => true,
                'endpoint' => $endpoint,
                'data' => $callback(),
            ]);
        } catch (RequestException $exception) {
            return response()->json([
                'status' => false,
                'http_status' => $exception->response->status(),
                'message' => $exception->response->json() ?: $exception->response->body(),
            ], $exception->response->status());
        } catch (Throwable $exception) {
            return response()->json([
                'status' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
