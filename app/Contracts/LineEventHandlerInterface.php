<?php

namespace App\Contracts;

use App\DTOs\LineEvent;

interface LineEventHandlerInterface
{
    /**
     * 處理事件
     *
     * @param  LineEvent $event
     * @return void
     */
    public function handle(LineEvent $event): void;
}
