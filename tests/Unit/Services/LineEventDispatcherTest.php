<?php

namespace Tests\Unit\Services;

use App\DTOs\LineEvent;
use App\Enums\LLMMode;
use App\Handlers\MessageEventHandler;
use App\Handlers\PostbackEventHandler;
use App\Handlers\FollowEventHandler;
use App\Handlers\UnfollowEventHandler;
use App\Services\LineEventDispatcher;
use Tests\TestCase;
use Mockery;
use Exception;

class LineEventDispatcherTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function newDispatcher(): LineEventDispatcher
    {
        return new LineEventDispatcher();
    }

    public function test_dispatch_message_event_to_message_handler(): void
    {
        $event = new LineEvent(
            eventType: LineEvent::TYPE_MESSAGE,
            messageType: LineEvent::MESSAGE_TYPE_TEXT,
            sourceType: 'user',
            userId: 'user123',
            messageId: 'mid123',
            text: 'hello',
            replyToken: 'token123',
            postbackData: null
        );

        $handler = Mockery::mock(MessageEventHandler::class);
        $handler->shouldReceive('handle')->once()->with($event);

        app()->instance(MessageEventHandler::class, $handler);

        $this->newDispatcher()->handleEvent($event);

        $this->assertTrue(true);
    }

    public function test_dispatch_postback_event_to_postback_handler(): void
    {
        $event = new LineEvent(
            eventType: LineEvent::TYPE_POSTBACK,
            messageType: null,
            sourceType: 'user',
            userId: 'user456',
            messageId: null,
            text: null,
            replyToken: 'token456',
            postbackData: LLMMode::OPENAI->value
        );

        $handler = Mockery::mock(PostbackEventHandler::class);
        $handler->shouldReceive('handle')->once()->with($event);

        app()->instance(PostbackEventHandler::class, $handler);

        $this->newDispatcher()->handleEvent($event);

        $this->assertTrue(true);
    }

    public function test_dispatch_follow_event_to_follow_handler(): void
    {
        $event = new LineEvent(
            eventType: LineEvent::TYPE_FOLLOW,
            messageType: null,
            sourceType: 'user',
            userId: 'user789',
            messageId: null,
            text: null,
            replyToken: 'token789',
            postbackData: null
        );

        $handler = Mockery::mock(FollowEventHandler::class);
        $handler->shouldReceive('handle')->once()->with($event);

        app()->instance(FollowEventHandler::class, $handler);

        $this->newDispatcher()->handleEvent($event);

        $this->assertTrue(true);
    }

    public function test_dispatch_unfollow_event_to_unfollow_handler(): void
    {
        $event = new LineEvent(
            eventType: LineEvent::TYPE_UNFOLLOW,
            messageType: null,
            sourceType: 'user',
            userId: 'user000',
            messageId: null,
            text: null,
            replyToken: 'token000',
            postbackData: null
        );

        $handler = Mockery::mock(UnfollowEventHandler::class);
        $handler->shouldReceive('handle')->once()->with($event);

        app()->instance(UnfollowEventHandler::class, $handler);

        $this->newDispatcher()->handleEvent($event);

        $this->assertTrue(true);
    }

    public function test_ignore_event_when_source_is_not_user(): void
    {
        $event = new LineEvent(
            eventType: LineEvent::TYPE_MESSAGE,
            messageType: LineEvent::MESSAGE_TYPE_TEXT,
            sourceType: 'group',
            userId: 'user123',
            messageId: 'mid123',
            text: 'hello',
            replyToken: 'token123',
            postbackData: null
        );

        $this->newDispatcher()->handleEvent($event);

        $this->assertTrue(true);
    }

    public function test_throw_exception_when_event_type_not_supported(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('不支援的事件型態');

        $event = new LineEvent(
            eventType: 'unknown',
            messageType: null,
            sourceType: 'user',
            userId: 'user999',
            messageId: null,
            text: null,
            replyToken: 'token999',
            postbackData: null
        );

        $this->newDispatcher()->handleEvent($event);
    }
}
