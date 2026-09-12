<?php

class Bot
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    private function query(string $method, array $params)
    {
        $str = 'https://api.telegram.org/bot' . $this->token . '/' . $method;
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $str,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ];
        curl_setopt_array($ch, $options);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true);
    }

    public function SendMessage(int|string $chatId,string $text, $replyMarkup = null)
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if (!empty($replyMarkup)) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->query('sendMessage', $params);
    }

    public function answerCallbackQuery(string $callbackQueryId, $text = null)
    {
        $params = [
            'callback_query_id' => $callbackQueryId
        ];

        if (!empty($text)) {
            $params['text'] = $text;
        }

        return $this->query('answerCallbackQuery', $params);
    }

    public function editMessageText(int|string $chatId, int $messageId, string $text, $replyMarkup = null)
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
        ];

        if (!empty($replyMarkup)) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->query('editMessageText', $params);
    }
}