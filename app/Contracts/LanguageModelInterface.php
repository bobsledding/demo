<?php

namespace App\Contracts;

interface LanguageModelInterface
{
    public function chat(array|string $input): string;

    public function analyze(string $binary): string;
}
