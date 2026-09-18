<?php

class Update
{
    private ?int $chatId = null;
    private ?int $userId = null;
    private ?string $text = null;
    private string $type = '';
    private ?int $messageId = null;

    public function __construct(array $data)
    {
        if (isset($data['message'])) {
            $this->chatId = $data['message']['chat']['id'];
            $this->userId = $data['message']['from']['id'];
            $this->messageId = $data['message']['message_id'];
            $this->text = $data['message']['text'] ?? '';
            $this->type = 'message';
        } elseif (isset($data['callback_query'])) {
            $this->chatId = $data['callback_query']['message']['chat']['id'];
            $this->userId = $data['callback_query']['from']['id'];
            $this->messageId = $data['callback_query']['message']['message_id'];
            $this->text = $data['callback_query']['data'];
            $this->type = 'callback_query';
        }
    }

    public function isValid(): bool {
        return $this->chatId !== null;
    }

    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getMessageId(): ?int
    {
        return $this->messageId;
    }
}
