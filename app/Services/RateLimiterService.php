<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RateLimiterService
{
    protected const COOLDOWN_SECONDS = 5;
    protected const CACHE_KEY_PREFIX = 'rate-limit-';

    public function hit(string $userId): bool
    {
        $key = $this->cacheKey($userId);
        $lastHitAt = Cache::get($key);

        if ($lastHitAt && now()->diffInSeconds($lastHitAt) < self::COOLDOWN_SECONDS) {
            return false;
        }

        Cache::put($key, now(), now()->addSeconds(self::COOLDOWN_SECONDS + 1));

        return true;
    }

    protected function cacheKey(string $userId): string
    {
        return self::CACHE_KEY_PREFIX . $userId;
    }
}
