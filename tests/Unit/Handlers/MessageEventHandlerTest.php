<?php

namespace Tests\Unit\Handlers;

use App\DTOs\LineEvent;
use App\Handlers\MessageEventHandler;
use App\Repositories\LLMModeRepository;
use App\Services\RateLimiterService;
use App\Services\UserMemoryService;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use Tests\TestCase;
use Mockery;

class MessageEventHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function newHandler()
    {
        return new MessageEventHandler(
            Mockery::mock(MessagingApiApi::class),
            Mockery::mock(MessagingApiBlobApi::class),
            app()->make(LLMModeRepository::class),
            Mockery::mock(UserMemoryService::class),
            Mockery::mock(RateLimiterService::class),
        );
    }

    public function test_block_when_rate_limited(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldReceive('replyMessage')->once();

        $rateLimiter = Mockery::mock(RateLimiterService::class);
        $rateLimiter->shouldReceive('hit')->once()->andReturn(false);

        $handler = new MessageEventHandler(
            $lineBot,
            Mockery::mock(MessagingApiBlobApi::class),
            app()->make(LLMModeRepository::class),
            Mockery::mock(UserMemoryService::class),
            $rateLimiter,
        );

        $event = new LineEvent(
            eventType: 'message',
            messageType: LineEvent::MESSAGE_TYPE_TEXT,
            sourceType: 'user',
            userId: 'user123',
            messageId: 'msg123',
            text: 'Hello',
            replyToken: 'replytoken123',
            postbackData: null,
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }

    public function test_switch_mode_when_receive_mode_command(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldReceive('replyMessage')->once();

        $rateLimiter = Mockery::mock(RateLimiterService::class);
        $rateLimiter->shouldReceive('hit')->once()->andReturn(true);

        $memoryService = Mockery::mock(UserMemoryService::class);

        $handler = new MessageEventHandler(
            $lineBot,
            Mockery::mock(MessagingApiBlobApi::class),
            app()->make(LLMModeRepository::class),
            $memoryService,
            $rateLimiter,
        );

        $event = new LineEvent(
            eventType: 'message',
            messageType: LineEvent::MESSAGE_TYPE_TEXT,
            sourceType: 'user',
            userId: 'user123',
            messageId: 'msg456',
            text: 'openai',
            replyToken: 'replytoken456',
            postbackData: null,
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }
}
