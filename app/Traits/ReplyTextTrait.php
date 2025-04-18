<?php

namespace App\Traits;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;

trait ReplyTextTrait
{
    protected MessagingApiApi $lineBot;

    protected function replyText(string $replyToken, string $text): void
    {
        $message = new TextMessage([
            'type' => 'text',
            'text' => $text,
        ]);

        $request = new ReplyMessageRequest([
            'replyToken' => $replyToken,
            'messages' => [$message],
        ]);

        $this->lineBot->replyMessage($request);
    }
}
