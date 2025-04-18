<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class RateLimiterService
{
    protected int $cooldownSeconds = 5;

    public function hit(string $userId): bool
    {
        $key = $this->cacheKey($userId);
        $lastHitAt = Cache::get($key);

        if ($lastHitAt && now()->diffInSeconds($lastHitAt) < $this->cooldownSeconds) {
            return false;
        }

        Cache::put($key, now(), now()->addSeconds($this->cooldownSeconds + 1));

        return true;
    }

    protected function cacheKey(string $userId): string
    {
        return "rate-limit-{$userId}";
    }
}
