<?php
require_once __DIR__ . '/../init.php';

if (isset($data['callback_query'])) {
    $callbackId = $data['callback_query']['id'];
    $chatId     = $data['callback_query']['message']['chat']['id'];
    $messageId  = $data['callback_query']['message']['message_id'];
    $telegramId = $data['callback_query']['from']['id'];
    $action     = $data['callback_query']['data'];

    if ($action === 'random_quote') {
        $quote = $quoteRepo->getRandomByUserId($telegramId);
        
        if ($quote !== null) {
            $bot->answerCallbackQuery($callbackId);
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🎲 Еще мысль', 'callback_data' => 'random_quote']],
                    [['text' => '➕ Добавить мысль', 'callback_data' => 'add_quote']],
                ]
            ];

            $bot->editMessageText($chatId, $messageId, $quote, $keyboard);

        } else {

            $bot->answerCallbackQuery($callbackId, 'В Чертогах пока пусто!');

        }
    }

    elseif ($action === 'add_quote') {

        $bot->answerCallbackQuery($callbackId);
        $userRepo->setStep($telegramId, 'waiting_quote');
        $bot->SendMessage($chatId, 'Напиши и отправь свою мысль прямо сюда. Я зафиксирую её в Чертогах.');

    }
}
