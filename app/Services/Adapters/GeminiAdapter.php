<?php

namespace App\Services\Adapters;

use App\Contracts\LanguageModelInterface;
use App\Services\LLM\GeminiService;

class GeminiAdapter implements LanguageModelInterface
{
    protected GeminiService $gemini;

    public function __construct()
    {
        $this->gemini = new GeminiService();
    }

    public function chat(array|string $input): string
    {
        $contents = is_string($input)
            ? [$this->getContent('user', $input)]
            : $this->toGeminiContents($input);
        return $this->gemini->chat($contents) ?? '（Gemini 回覆錯誤）';
    }

    public function analyze(string $binary): string
    {
        $base64 = base64_encode($binary);
        return $this->gemini->sendImage($base64) ?? '（Gemini 圖片解析錯誤）';
    }

    protected function toGeminiContents(array $messages): array
    {
        $contents = [];

        foreach ($messages as $message) {
            $role = ($message['role'] === 'assistant') ? 'model' : 'user';

            $contents[] = $this->getContent($role, $message['content']);
        }

        return $contents;
    }

    protected function getContent(string $role, string $text): array
    {
        return [
            'role' => $role,
            'parts' => [
                ['text' => $text]
            ]
        ];
    }
}
