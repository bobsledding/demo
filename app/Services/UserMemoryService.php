<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class UserMemoryService
{
    public const MAX_HISTORY = 10;
    private const CACHE_PREFIX = 'line-user-history-';

    public function getHistory(string $userId): array
    {
        return Cache::get($this->getCacheKey($userId), []);
    }

    public function addUserMessage(string $userId, string $text): void
    {
        $history = $this->getHistory($userId);
        $history[] = [
            'role' => 'user',
            'content' => $text,
        ];

        $this->saveHistory($userId, $history);
    }

    public function addAssistantReply(string $userId, string $text): void
    {
        $history = $this->getHistory($userId);
        $history[] = [
            'role' => 'assistant',
            'content' => $text,
        ];

        $this->saveHistory($userId, $history);
    }

    private function saveHistory(string $userId, array $history): void
    {
        $history = array_slice($history, -self::MAX_HISTORY);
        Cache::put($this->getCacheKey($userId), $history, now()->addDays(7));
    }

    private function getCacheKey(string $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }
}
