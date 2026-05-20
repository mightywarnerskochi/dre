<?php

namespace App\Services;

use App\Models\AccessToken;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class PropertyFinderApiService
{
    private const PROVIDER = 'property_finder';

    public function __construct(
        private readonly ?string $baseUrl = null,
        private readonly ?string $apiKey = null,
        private readonly ?string $apiSecret = null,
    ) {}

    public function getAccessToken(bool $forceRefresh = false): string
    {
        $storedToken = AccessToken::query()->where('provider', self::PROVIDER)->first();

        if (! $forceRefresh && $storedToken && ! $storedToken->isExpired()) {
            return $storedToken->access_token;
        }

        return $this->createAccessToken();
    }

    public function createAccessToken(): string
    {
        $response = $this->sendWithoutToken('post', '/v1/auth/token', [
            'apiKey' => $this->credential('api_key'),
            'apiSecret' => $this->credential('api_secret'),
        ], isTokenRequest: true);

        $accessToken = data_get($response->json(), 'accessToken');
        $expiresIn = (int) data_get($response->json(), 'expiresIn', 1800);
        $tokenType = data_get($response->json(), 'tokenType', 'Bearer');
        $issuedAt = now();

        if (blank($accessToken)) {
            throw new RuntimeException('Property Finder token response did not contain accessToken.');
        }

        AccessToken::query()->updateOrCreate(
            ['provider' => self::PROVIDER],
            [
                'access_token' => $accessToken,
                'token_type' => $tokenType,
                'expires_in' => $expiresIn,
                'issued_at' => $issuedAt,
                'expires_at' => $issuedAt->copy()->addSeconds(max($expiresIn - 60, 60)),
                'metadata' => [
                    'base_url' => $this->baseUri(),
                ],
            ],
        );

        return $accessToken;
    }

    public function request(string $method, string $endpoint, array $payload = [], array $query = []): Response
    {
        $response = $this->sendWithToken($method, $endpoint, $payload, $query);

        if ($response->unauthorized()) {
            $this->forgetAccessToken();
            $response = $this->sendWithToken($method, $endpoint, $payload, $query, forceRefresh: true);
        }

        return $response;
    }

    public function get(string $endpoint, array $query = []): array
    {
        return $this->json($this->request('get', $endpoint, query: $query));
    }

    public function post(string $endpoint, array $payload = [], array $query = []): array
    {
        return $this->json($this->request('post', $endpoint, $payload, $query));
    }

    public function patch(string $endpoint, array $payload = [], array $query = []): array
    {
        return $this->json($this->request('patch', $endpoint, $payload, $query));
    }

    public function put(string $endpoint, array $payload = [], array $query = []): array
    {
        return $this->json($this->request('put', $endpoint, $payload, $query));
    }

    public function delete(string $endpoint, array $payload = [], array $query = []): array
    {
        return $this->json($this->request('delete', $endpoint, $payload, $query));
    }

    public function paginate(string $endpoint, array $query = [], int $perPage = 100, int $maxPages = 0): array
    {
        $page = max((int) ($query['page'] ?? 1), 1);
        $perPage = min(max($perPage, 1), 100);
        $items = [];
        $lastResponse = null;
        $pagesFetched = 0;

        do {
            $lastResponse = $this->get($endpoint, array_merge($query, [
                'page' => $page,
                'perPage' => $perPage,
            ]));

            $items = array_merge($items, $this->responseItems($lastResponse));
            $totalPages = (int) data_get($lastResponse, 'pagination.totalPages', $page);
            $page++;
            $pagesFetched++;
        } while ($page <= $totalPages && ($maxPages === 0 || $pagesFetched < $maxPages));

        return [
            'pagination' => data_get($lastResponse, 'pagination', []),
            'pagesFetched' => $pagesFetched,
            'results' => $items,
        ];
    }

    public function searchUsers(array $query = []): array
    {
        return $this->get('/v1/users', $query);
    }

    public function allUsers(array $query = [], int $perPage = 100, int $maxPages = 0): array
    {
        return $this->paginate('/v1/users', $query, $perPage, $maxPages);
    }

    public function createUser(array $payload): array
    {
        return $this->post('/v1/users', $payload);
    }

    public function updatePrivateProfile(string|int $id, array $payload): array
    {
        return $this->patch("/v1/users/{$id}", $payload);
    }

    public function updatePublicProfile(string|int $id, array $payload): array
    {
        return $this->patch("/v1/public-profiles/{$id}", $payload);
    }

    public function submitVerificationRequest(string|int $id, array $payload = []): array
    {
        return $this->post("/v1/public-profiles/{$id}/submit-verification", $payload);
    }

    public function roles(array $query = []): array
    {
        return $this->get('/v1/roles', $query);
    }

    public function searchListings(array $query = []): array
    {
        return $this->get('/v1/listings', $this->withPaginationDefaults($query));
    }

    public function allListings(array $query = [], int $perPage = 100, int $maxPages = 0): array
    {
        return $this->paginate('/v1/listings', $query, $perPage, $maxPages);
    }

    public function createListing(array $payload): array
    {
        return $this->post('/v1/listings', $payload);
    }

    public function updateListing(string|int $id, array $payload): array
    {
        return $this->put("/v1/listings/{$id}", $payload);
    }

    public function deleteListing(string|int $id): array
    {
        return $this->delete("/v1/listings/{$id}");
    }

    public function publishListing(string|int $id, array $payload = []): array
    {
        return $this->post("/v1/listings/{$id}/publish", $payload);
    }

    public function unpublishListing(string|int $id, array $payload = []): array
    {
        return $this->post("/v1/listings/{$id}/unpublish", $payload);
    }

    public function listingPublishPrice(string|int $id, array $query = []): array
    {
        return $this->get("/v1/listings/{$id}/publish/prices", $query);
    }

    public function upgradeListing(string|int $id, array $payload): array
    {
        return $this->post("/v1/listings/{$id}/upgrades", $payload);
    }

    public function availableUpgrades(string|int $id, array $query = []): array
    {
        return $this->get("/v1/listings/{$id}/upgrades", $query);
    }

    public function floorPlans(array $query = []): array
    {
        return $this->get('/v1/floor-plans', $this->withPaginationDefaults($query));
    }

    public function floorPlan(string|int $id): array
    {
        return $this->get("/v1/floor-plans/{$id}");
    }

    public function permit(string $permitNumber, string $licenseNumber): array
    {
        return $this->get("/v1/compliances/{$permitNumber}/{$licenseNumber}");
    }

    public function listingVerificationSubmissions(array $query = []): array
    {
        return $this->get('/v1/listing-verifications', $this->withPaginationDefaults($query));
    }

    public function submitListingVerification(array $payload): array
    {
        return $this->post('/v1/listing-verifications', $payload);
    }

    public function resubmitListingVerification(string|int $submissionId, array $payload = []): array
    {
        return $this->post("/v1/listing-verifications/{$submissionId}/resubmit", $payload);
    }

    public function listingEligibilityCheck(array $payload): array
    {
        return $this->post('/v1/listing-verifications/eligibility-check', $payload);
    }

    public function locations(array $query = []): array
    {
        return $this->get('/v1/locations', $this->withPaginationDefaults($query));
    }

    public function allLocations(array $query = [], int $perPage = 100, int $maxPages = 0): array
    {
        return $this->paginate('/v1/locations', $query, $perPage, $maxPages);
    }

    public function leads(array $query = []): array
    {
        return $this->get('/v1/leads', $this->withPaginationDefaults($query));
    }

    public function project(string|int $id): array
    {
        return $this->get("/v1/projects/{$id}");
    }

    public function publicProfileStats(array $query = []): array
    {
        return $this->get('/v1/stats/public-profiles', $query);
    }

    public function superAgentStats(array $query = []): array
    {
        return $this->get('/v1/stats/superagent-stats', $query);
    }

    public function publicProfileRankings(array $query = []): array
    {
        return $this->get('/v1/stats/public-profiles-arena-ranking', $query);
    }

    public function topPublicProfiles(array $query = []): array
    {
        return $this->get('/v1/stats/top-public-profiles', $query);
    }

    public function creditBalance(array $query = []): array
    {
        return $this->get('/v1/credits/balance', $query);
    }

    public function creditTransactions(array $query = []): array
    {
        return $this->get('/v1/credits/transactions', $this->withPaginationDefaults($query));
    }

    public function webhooks(array $query = []): array
    {
        return $this->get('/v1/webhooks', $query);
    }

    public function subscribeWebhook(array $payload): array
    {
        return $this->post('/v1/webhooks', $payload);
    }

    public function deleteWebhook(string|int $eventId): array
    {
        return $this->delete("/v1/webhooks/{$eventId}");
    }

    public function forgetAccessToken(): void
    {
        AccessToken::query()->where('provider', self::PROVIDER)->delete();
    }

    private function sendWithToken(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
        bool $forceRefresh = false,
    ): Response {
        return $this->send($method, $endpoint, $payload, $query, $this->getAccessToken($forceRefresh));
    }

    private function sendWithoutToken(string $method, string $endpoint, array $payload = [], bool $isTokenRequest = false): Response
    {
        return $this->send($method, $endpoint, $payload, [], null, $isTokenRequest);
    }

    private function send(
        string $method,
        string $endpoint,
        array $payload = [],
        array $query = [],
        ?string $accessToken = null,
        bool $isTokenRequest = false,
    ): Response {
        $this->waitForRateLimit($isTokenRequest);
        $query = $this->normalizeQuery($query);

        $request = Http::baseUrl($this->baseUri())
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(3, function (int $attempt, mixed $exception) {
                if ($exception instanceof RequestException && $exception->response?->status() === 429) {
                    return ((int) $exception->response->header('Retry-After', 0) * 1000) ?: ($attempt * 1000);
                }

                return $attempt * 500;
            }, function (\Throwable $exception) {
                if ($exception instanceof RequestException) {
                    return $exception->response?->status() === 429 || $exception->response?->serverError();
                }

                return true;
            }, throw: false);

        if ($accessToken) {
            $request = $request->withToken($accessToken);
        }

        $response = match (Str::lower($method)) {
            'get' => $request->get($endpoint, $query),
            'post' => $request->post($endpoint, $payload),
            'patch' => $request->patch($endpoint, $payload),
            'put' => $request->put($endpoint, $payload),
            'delete' => $request->delete($endpoint, $payload),
            default => throw new RuntimeException("Unsupported Property Finder HTTP method [{$method}]."),
        };

        if ($response->failed() && ! ($response->unauthorized() && ! $isTokenRequest)) {
            $response->throw();
        }

        return $response;
    }

    private function json(Response $response): array
    {
        return $response->json() ?: [];
    }

    private function responseItems(array $response): array
    {
        $items = data_get($response, 'results', data_get($response, 'data.items', data_get($response, 'data', [])));

        return is_array($items) ? $items : [];
    }

    private function normalizeQuery(array $query): array
    {
        foreach ($query as $key => $value) {
            if (is_bool($value)) {
                $query[$key] = $value ? 'true' : 'false';
            }
        }

        return $query;
    }

    private function baseUri(): string
    {
        return rtrim($this->baseUrl ?: config('prop.api_url'), '/');
    }

    private function credential(string $key): string
    {
        $value = match ($key) {
            'api_key' => $this->apiKey ?: config('prop.api_key'),
            'api_secret' => $this->apiSecret ?: config('prop.api_secret'),
            default => null,
        };

        if (blank($value)) {
            throw new RuntimeException("Property Finder {$key} is missing.");
        }

        return $value;
    }

    private function withPaginationDefaults(array $query): array
    {
        $query['perPage'] = min(max((int) ($query['perPage'] ?? 10), 1), 100);
        $query['page'] = max((int) ($query['page'] ?? 1), 1);

        return $query;
    }

    private function waitForRateLimit(bool $isTokenRequest): void
    {
        $limit = $isTokenRequest ? 60 : 650;
        $window = now()->format('YmdHi');
        $key = 'property_finder_rate_limit:'.($isTokenRequest ? 'token' : 'api').":{$window}";

        Cache::add($key, 0, now()->addMinute());

        if ((int) Cache::get($key, 0) >= $limit) {
            sleep(now()->diffInSeconds(now()->copy()->addMinute()->startOfMinute()) + 1);
        }

        Cache::increment($key);
    }
}
