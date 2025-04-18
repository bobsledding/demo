<?php

namespace Tests\Unit\Handlers;

use App\DTOs\LineEvent;
use App\Enums\LLMMode;
use App\Handlers\PostbackEventHandler;
use App\Repositories\LLMModeRepository;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use Tests\TestCase;
use Mockery;

class PostbackEventHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function newHandler($lineBot = null, $repository = null): PostbackEventHandler
    {
        return new PostbackEventHandler(
            $lineBot ?? Mockery::mock(MessagingApiApi::class),
            $repository ?? Mockery::mock(LLMModeRepository::class),
        );
    }

    public function test_do_nothing_when_postback_data_is_empty(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldNotReceive('replyMessage');
        $repository = Mockery::mock(LLMModeRepository::class);
        $repository->shouldNotReceive('switchTo');

        $handler = $this->newHandler($lineBot, $repository);

        $event = new LineEvent(
            eventType: LineEvent::TYPE_POSTBACK,
            messageType: null,
            sourceType: 'user',
            userId: 'user789',
            messageId: null,
            text: null,
            replyToken: 'reply-token-empty',
            postbackData: null,
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }

    public function test_do_nothing_when_postback_data_is_invalid(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldNotReceive('replyMessage');
        $repository = Mockery::mock(LLMModeRepository::class);
        $repository->shouldNotReceive('switchTo');

        $handler = $this->newHandler($lineBot, $repository);

        $event = new LineEvent(
            eventType: LineEvent::TYPE_POSTBACK,
            messageType: null,
            sourceType: 'user',
            userId: 'user123',
            messageId: null,
            text: null,
            replyToken: 'replytoken123',
            postbackData: 'invalid-mode',
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }

    public function test_switch_to_openai_when_postback_data_is_openai(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldReceive('replyMessage')->once();
        $repository = Mockery::mock(LLMModeRepository::class);
        $repository->shouldReceive('switchTo')->once()->with('user456', LLMMode::OPENAI);

        $handler = $this->newHandler($lineBot, $repository);

        $event = new LineEvent(
            eventType: LineEvent::TYPE_POSTBACK,
            messageType: null,
            sourceType: 'user',
            userId: 'user456',
            messageId: null,
            text: null,
            replyToken: 'replytoken456',
            postbackData: LLMMode::OPENAI->value,
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }

    public function test_switch_to_gemini_when_postback_data_is_gemini(): void
    {
        $lineBot = Mockery::mock(MessagingApiApi::class);
        $lineBot->shouldReceive('replyMessage')->once();
        $repository = Mockery::mock(LLMModeRepository::class);
        $repository->shouldReceive('switchTo')->once()->with('user789', LLMMode::GEMINI);

        $handler = $this->newHandler($lineBot, $repository);

        $event = new LineEvent(
            eventType: LineEvent::TYPE_POSTBACK,
            messageType: null,
            sourceType: 'user',
            userId: 'user789',
            messageId: null,
            text: null,
            replyToken: 'replytoken789',
            postbackData: LLMMode::GEMINI->value,
        );

        $handler->handle($event);

        $this->assertTrue(true);
    }
}
