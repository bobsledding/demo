<?php

namespace App\Contracts;

use App\DTOs\LineEvent;

interface LineEventHandlerInterface
{
    public function handle(LineEvent $event): void;
}
