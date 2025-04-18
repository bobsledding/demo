<?php

namespace Tests\Unit\Handlers;

use App\DTOs\LineEvent;
use App\Handlers\UnfollowEventHandler;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UnfollowEventHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
    }

    public function test_log_when_user_unfollows(): void
    {
        $handler = new UnfollowEventHandler();

        $event = new LineEvent(
            eventType: LineEvent::TYPE_UNFOLLOW,
            messageType: null,
            sourceType: 'user',
            userId: 'user456',
            messageId: null,
            text: null,
            replyToken: null,
            postbackData: null,
        );

        $handler->handle($event);

        Log::shouldHaveReceived('info')
            ->once()
            ->with('使用者解除好友', [
                'userId' => 'user456',
            ]);
    }
}
