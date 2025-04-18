<?php

namespace Tests\Unit\Handlers;

use App\DTOs\LineEvent;
use App\Handlers\FollowEventHandler;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FollowEventHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    public function test_log_when_user_follows(): void
    {
        $handler = new FollowEventHandler();

        $event = new LineEvent(
            eventType: 'follow',
            messageType: null,
            sourceType: 'user',
            userId: 'user123',
            messageId: null,
            text: null,
            replyToken: null,
            postbackData: null,
        );

        $handler->handle($event);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('使用者加入好友', [
                'userId' => 'user123',
            ]);
    }
}
