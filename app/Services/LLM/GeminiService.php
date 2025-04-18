<?php

namespace App\Services\LLM;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
    }

    public function chat(array $contents): ?string
    {
        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey, [
            'contents' => $contents,
        ]);

        return $response->json('candidates.0.content.parts.0.text');
    }

    public function sendImage(string $base64): ?string
    {
        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey, [
            'contents' => [[
                'parts' => [
                    [
                        'inlineData' => [
                            'mimeType' => 'image/jpeg',
                            'data' => $base64,
                        ],
                    ],
                    [
                        'text' => '請描述這張圖片的內容',
                    ],
                ],
            ]],
        ]);

        return $response->json('candidates.0.content.parts.0.text');
    }
}
