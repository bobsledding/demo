<?php

namespace Tests\Unit\DTOs;

use App\DTOs\LineEvent;
use PHPUnit\Framework\TestCase;

class LineEventTest extends TestCase
{
    public function test_from_array(): void
    {
        $data = [
            'type' => 'message',
            'message' => [
                'type' => 'text',
                'id' => 'message123',
                'text' => 'Hello',
            ],
            'source' => [
                'type' => 'user',
                'userId' => 'user123',
            ],
            'replyToken' => 'replytoken123',
            'postback' => [
                'data' => 'some-data',
            ],
        ];

        $event = LineEvent::from($data);

        $this->assertEquals('message', $event->eventType);
        $this->assertEquals(LineEvent::MESSAGE_TYPE_TEXT, $event->messageType);
        $this->assertEquals('user', $event->sourceType);
        $this->assertEquals('user123', $event->userId);
        $this->assertEquals('message123', $event->messageId);
        $this->assertEquals('Hello', $event->text);
        $this->assertEquals('replytoken123', $event->replyToken);
        $this->assertEquals('some-data', $event->postbackData);
    }

    public function test_is_methods(): void
    {
        $event = new LineEvent(
            eventType: 'message',
            messageType: 'text',
            sourceType: 'user',
            userId: 'user123',
            messageId: 'message123',
            text: 'Hello',
            replyToken: 'replytoken123',
            postbackData: 'data123',
        );

        $this->assertTrue($event->isMessage());
        $this->assertFalse($event->isPostback());
        $this->assertFalse($event->isFollow());
        $this->assertFalse($event->isUnfollow());
        $this->assertTrue($event->isTextMessage());
        $this->assertFalse($event->isImageMessage());
        $this->assertTrue($event->isUserSource());
    }

    public function test_null_safe_behavior(): void
    {
        $event = new LineEvent(
            eventType: 'postback',
            messageType: null,
            sourceType: null,
            userId: null,
            messageId: null,
            text: null,
            replyToken: null,
            postbackData: null,
        );

        $this->assertFalse($event->isMessage());
        $this->assertTrue($event->isPostback());
        $this->assertFalse($event->isTextMessage());
        $this->assertFalse($event->isImageMessage());
        $this->assertFalse($event->isUserSource());
    }
}
