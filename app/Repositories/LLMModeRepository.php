<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use App\Enums\LLMMode;

class LLMModeRepository
{
    protected function cacheKey(string $userId): string
    {
        return "line-user-mode-{$userId}";
    }

    public function getMode(string $userId): LLMMode
    {
        return Cache::get($this->cacheKey($userId), LLMMode::OPENAI);
    }

    public function switchTo(string $userId, LLMMode $mode): void
    {
        Cache::put($this->cacheKey($userId), $mode, now()->addDays(7));
    }
}
