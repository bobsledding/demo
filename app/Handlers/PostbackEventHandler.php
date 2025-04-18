<?php

namespace App\Handlers;

use App\Contracts\LineEventHandlerInterface;
use App\DTOs\LineEvent;
use App\Enums\LLMMode;
use App\Repositories\LLMModeRepository;
use App\Traits\ReplyTextTrait;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;

class PostbackEventHandler implements LineEventHandlerInterface
{
    use ReplyTextTrait;

    public function __construct(
        protected MessagingApiApi $lineBot,
        protected LLMModeRepository $repository
    ) {}

    public function handle(LineEvent $event): void
    {
        if (empty($event->postbackData)) {
            return;
        }

        $tryMode = LLMMode::tryFrom(strtolower($event->postbackData));

        if (!$tryMode) {
            return;
        }

        $this->repository->switchTo($event->userId, $tryMode);
        $this->replyText($event->replyToken, $tryMode->switchMessage());
    }
}
