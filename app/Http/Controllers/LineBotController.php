<?php

namespace App\Http\Controllers;

use App\Services\LineEventDispatcher;
use App\Http\Requests\LineWebhookRequest;
use App\DTOs\LineEvent;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LineBotController extends Controller
{
    protected LineEventDispatcher $dispatcher;

    public function __construct(LineEventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * 透過LLM回覆Line訊息
     *
     * @param  mixed $request
     * @return Response
     */
    public function webhook(LineWebhookRequest $request): Response
    {
        $events = $request->input('events', []);

        foreach ($events as $event) {
            try {
                $this->dispatcher->handleEvent(LineEvent::from($event));
            } catch (Exception $e) {
                Log::error('Line Event 處理失敗', [
                    'event' => [
                        'type' => $event['type'] ?? null,
                        'userId' => $event['source']['userId'] ?? null,
                        'text' => $event['message']['text'] ?? null,
                    ],
                    'error_message' => $e->getMessage(),
                    'error_file' => $e->getFile(),
                    'error_line' => $e->getLine(),
                    'trace' => collect($e->getTrace())->take(5)->toArray(),
                ]);
            }
        }

        return response()->noContent();
    }
}
