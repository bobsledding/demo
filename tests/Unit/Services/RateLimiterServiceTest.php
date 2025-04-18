<?php

namespace Tests\Unit\Services;

use App\Services\RateLimiterService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RateLimiterServiceTest extends TestCase
{
    protected RateLimiterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new RateLimiterService();
    }

    public function test_hit_allows_first_request()
    {
        $userId = 'test-user';

        $result = $this->service->hit($userId);

        $this->assertTrue($result, 'First request should be allowed.');
    }

    public function test_hit_blocks_within_cooldown()
    {
        $userId = 'test-user';

        $this->service->hit($userId);
        $result = $this->service->hit($userId);

        $this->assertFalse($result, 'Second request within cooldown should be blocked.');
    }

    public function test_hit_allows_after_cooldown()
    {
        $userId = 'test-user';

        $this->service->hit($userId);

        Cache::put("rate-limit-{$userId}", now()->subSeconds(10), now()->addSeconds(5));

        $result = $this->service->hit($userId);

        $this->assertTrue($result, 'Request after cooldown should be allowed.');
    }
}
