<?php

namespace App\DTOs;

class LineEvent
{
    public const TYPE_MESSAGE = 'message';
    public const TYPE_POSTBACK = 'postback';
    public const TYPE_FOLLOW = 'follow';
    public const TYPE_UNFOLLOW = 'unfollow';

    public const MESSAGE_TYPE_TEXT = 'text';
    public const MESSAGE_TYPE_IMAGE = 'image';

    public function __construct(
        public readonly string $eventType,
        public readonly ?string $messageType,
        public readonly ?string $sourceType,
        public readonly ?string $userId,
        public readonly ?string $messageId,
        public readonly ?string $text,
        public readonly ?string $replyToken,
        public readonly ?string $postbackData,
    ) {
    }

    public static function from(array $event): self
    {
        return new self(
            eventType: $event['type'] ?? '',
            messageType: $event['message']['type'] ?? null,
            sourceType: $event['source']['type'] ?? null,
            userId: $event['source']['userId'] ?? null,
            messageId: $event['message']['id'] ?? null,
            text: $event['message']['text'] ?? null,
            replyToken: $event['replyToken'] ?? null,
            postbackData: $event['postback']['data'] ?? null,
        );
    }

    public function isMessage(): bool
    {
        return $this->eventType === self::TYPE_MESSAGE;
    }

    public function isPostback(): bool
    {
        return $this->eventType === self::TYPE_POSTBACK;
    }

    public function isFollow(): bool
    {
        return $this->eventType === self::TYPE_FOLLOW;
    }

    public function isUnfollow(): bool
    {
        return $this->eventType === self::TYPE_UNFOLLOW;
    }

    public function isTextMessage(): bool
    {
        return $this->isMessage() && $this->messageType === self::MESSAGE_TYPE_TEXT;
    }

    public function isImageMessage(): bool
    {
        return $this->isMessage() && $this->messageType === self::MESSAGE_TYPE_IMAGE;
    }

    public function isUserSource(): bool
    {
        return ($this->sourceType ?? '') === 'user';
    }
}
