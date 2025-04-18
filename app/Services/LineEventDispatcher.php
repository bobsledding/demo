<?php

namespace App\Services;

use App\Contracts\LineEventHandlerInterface;
use App\DTOs\LineEvent;
use App\Handlers\PostbackEventHandler;
use App\Handlers\MessageEventHandler;
use App\Handlers\FollowEventHandler;
use App\Handlers\UnfollowEventHandler;
use App\Repositories\LLMModeRepository;
use Exception;

class LineEventDispatcher
{
    protected LLMModeRepository $llmmode_repository;

    public function handleEvent(LineEvent $event): void
    {
        if (!$event->isUserSource()) {
            return;
        }

        $handler = $this->resolveHandler($event);
        $handler->handle($event);
    }

    protected function resolveHandler(LineEvent $event): LineEventHandlerInterface
    {
        return match (true) {
            $event->isMessage() => app()->make(MessageEventHandler::class),
            $event->isPostback() => app()->make(PostbackEventHandler::class),
            $event->isFollow() => app()->make(FollowEventHandler::class),
            $event->isUnfollow() => app()->make(UnfollowEventHandler::class),
            default => throw new Exception('不支援的事件型態')
        };
    }
}
