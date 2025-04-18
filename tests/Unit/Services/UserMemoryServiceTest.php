<?php

namespace Tests\Unit\Services;

use App\Services\UserMemoryService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserMemoryServiceTest extends TestCase
{
    protected UserMemoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new UserMemoryService();
    }

    public function test_it_can_add_and_get_user_message()
    {
        $userId = 'test-user';
        $message = 'Hello, this is a user message.';

        $this->service->addUserMessage($userId, $message);
        $history = $this->service->getHistory($userId);

        $this->assertCount(1, $history);
        $this->assertEquals('user', $history[0]['role']);
        $this->assertEquals($message, $history[0]['content']);
    }

    public function test_it_can_add_and_get_assistant_reply()
    {
        $userId = 'test-user';
        $message = 'Hello, this is an assistant reply.';

        $this->service->addAssistantReply($userId, $message);
        $history = $this->service->getHistory($userId);

        $this->assertCount(1, $history);
        $this->assertEquals('assistant', $history[0]['role']);
        $this->assertEquals($message, $history[0]['content']);
    }

    public function test_it_limits_history_to_max_entries()
    {
        $userId = 'test-user';

        for ($i = 1; $i <= 15; $i++) {
            $this->service->addUserMessage($userId, "Message {$i}");
        }

        $history = $this->service->getHistory($userId);

        // MAX_HISTORY 是 10，應該只保留最後10筆
        $this->assertCount($this->service::MAX_HISTORY, $history);
        $this->assertEquals('Message 6', $history[0]['content']);
        $this->assertEquals('Message 15', $history[9]['content']);
    }
}
