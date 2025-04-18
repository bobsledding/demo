<?php

namespace App\Handlers;

use App\Contracts\LineEventHandlerInterface;
use App\DTOs\LineEvent;
use App\Services\UserMemoryService;
use App\Services\RateLimiterService;
use App\Factories\LLMServiceFactory;
use App\Traits\ReplyTextTrait;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Api\MessagingApiBlobApi;
use App\Enums\LLMMode;
use App\Repositories\LLMModeRepository;

class MessageEventHandler implements LineEventHandlerInterface
{
    use ReplyTextTrait;

    public function __construct(
        protected MessagingApiApi $lineBot,
        protected MessagingApiBlobApi $lineBlob,
        protected LLMModeRepository $repository,
        protected UserMemoryService $memoryService,
        protected RateLimiterService $rateLimiterService,
    ) {
    }

    public function handle(LineEvent $event): void
    {
        if (! $this->rateLimiterService->hit($event->userId)) {
            $this->replyText($event->replyToken, '請稍後再試');
            return;
        }
        $mode = $this->repository->getMode($event->userId);
        $llm = LLMServiceFactory::make($mode);
        $reply = '';

        if ($event->isImageMessage()) {
            $binaryStream = $this->lineBlob->getMessageContent($event->messageId);
            $binary = $binaryStream->fread($binaryStream->getSize());
            $reply = $llm->analyze($binary);
        } elseif ($event->isTextMessage()) {
            $text = $event->text ?? '';

            $tryMode = LLMMode::tryFrom(strtolower($text));
            if ($tryMode) {
                $this->repository->switchTo($event->userId, $tryMode);
                $reply = $tryMode->switchMessage();
            } else {
                $history = $this->memoryService->getHistory($event->userId);
                $history[] = [
                    'role' => 'user',
                    'content' => $text,
                ];

                $reply = $llm->chat($history);

                $this->memoryService->addUserMessage($event->userId, $text);
                $this->memoryService->addAssistantReply($event->userId, $reply);
            }
        } else {
            $reply = '不支援的訊息類型';
        }

        $this->replyText($event->replyToken, $reply);
    }
}
