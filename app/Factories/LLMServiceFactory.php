<?php

namespace App\Factories;

use App\Contracts\LanguageModelInterface;
use App\Enums\LLMMode;
use App\Services\Adapters\GeminiAdapter;
use App\Services\LLM\OpenAIService;

class LLMServiceFactory
{
    public static function make(LLMMode $mode): LanguageModelInterface
    {
        return match ($mode) {
            LLMMode::GEMINI => app()->make(GeminiAdapter::class),
            default => app()->make(OpenAIService::class),
        };
    }
}
