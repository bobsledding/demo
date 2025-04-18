<?php

namespace App\Handlers;

use App\Contracts\LineEventHandlerInterface;
use App\DTOs\LineEvent;
use Illuminate\Support\Facades\Log;

class FollowEventHandler implements LineEventHandlerInterface
{
    public function handle(LineEvent $event): void
    {
        Log::info('使用者加入好友', [
            'userId' => $event->userId,
        ]);
    }
}
