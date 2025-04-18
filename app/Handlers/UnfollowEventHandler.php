<?php

namespace App\Handlers;

use App\Contracts\LineEventHandlerInterface;
use App\DTOs\LineEvent;
use Illuminate\Support\Facades\Log;

class UnfollowEventHandler implements LineEventHandlerInterface
{
    public function handle(LineEvent $event): void
    {
        Log::info('使用者解除好友', [
            'userId' => $event->userId,
        ]);
    }
}
