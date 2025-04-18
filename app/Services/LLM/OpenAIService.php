<?php

namespace App\Services\LLM;

use App\Contracts\LanguageModelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIService implements LanguageModelInterface
{
    protected string $apiKey;
    protected string $model = 'gpt-4o';
    protected string $url = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function chat(array|string $input): string
    {
        $messages = is_string($input)
            ? [['role' => 'user', 'content' => $input]]
            : $input;

        try {
            $response = Http::withToken($this->apiKey)->post($this->url, [
                'model' => $this->model,
                'messages' => $messages,
            ]);

            return $response->json('choices.0.message.content') ?? '回覆失敗';
        } catch (Exception $e) {
            Log::error('OpenAI chat error', ['message' => $e->getMessage()]);
            return '（OpenAI 回覆錯誤，請稍後再試）';
        }
    }

    public function analyze(string $binary): string
    {
        try {
            $base64Image = base64_encode($binary);

            $response = Http::withToken($this->apiKey)->post($this->url, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => '這張圖片的內容是什麼？'],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/jpeg;base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ]
                ]
            ]);

            return $response->json('choices.0.message.content') ?? '圖片解析失敗';
        } catch (Exception $e) {
            Log::error('OpenAI analyze error', ['message' => $e->getMessage()]);
            return '（OpenAI 圖片解析失敗）';
        }
    }
}
