<?php

namespace Tests\Unit\Factories;

use App\Factories\LLMServiceFactory;
use App\Enums\LLMMode;
use App\Services\Adapters\GeminiAdapter;
use App\Services\LLM\OpenAIService;
use Tests\TestCase;

class LLMServiceFactoryTest extends TestCase
{
    public function test_it_returns_openai_service_when_mode_is_openai()
    {
        $service = LLMServiceFactory::make(LLMMode::OPENAI);
        $this->assertInstanceOf(OpenAIService::class, $service);
    }

    public function test_it_returns_gemini_adapter_when_mode_is_gemini()
    {
        $service = LLMServiceFactory::make(LLMMode::GEMINI);
        $this->assertInstanceOf(GeminiAdapter::class, $service);
    }
}
