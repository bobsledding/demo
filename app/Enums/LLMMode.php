<?php

namespace App\Enums;

enum LLMMode: string
{
    case OPENAI = 'openai';
    case GEMINI = 'gemini';

    public function switchMessage(): string
    {
        return match ($this) {
            self::OPENAI => '已切換為 OpenAI',
            self::GEMINI => '已切換為 Gemini',
        };
    }
}
