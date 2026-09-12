<?php
require_once __DIR__ . '/../init.php';

if (isset($data['message'])) {
    $chatId     = $data['message']['chat']['id'];
    $telegramId = $data['message']['from']['id'];
    $text       = trim($data['message']['text'] ?? '');
    $firstName  = $data['message']['from']['first_name'] ?? '';
    $username   = $data['message']['from']['username'] ?? null;

    $userRepo->createOrUpdate($telegramId, $firstName, $username);

    $step = $userRepo->getStep($telegramId);

    if ($step === 'waiting_quote') {

        if (empty($text)) {
            $bot->SendMessage($chatId, 'Чертоги принимают только текстовые мысли. Попробуй еще раз:');
            exit;
        }

        $quoteRepo->create($telegramId, $text);
        $bot->SendMessage($chatId, 'Мысль зафиксирована в Чертогах.');
        $userRepo->setStep($telegramId, null);
        exit;
    }

    if ($text === '/start') {

        $count = $quoteRepo->countByUserId($telegramId);

        if ($count === 0) {
            $msg = 'Чертоги пока пусты.';
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '➕ Добавить первую мысль', 'callback_data' => 'add_quote']]
                ]
            ];

            $bot->SendMessage($chatId, $msg, $keyboard);
        }

        else {
            $msg = "С возвращением. В чертогах сохранено {$count} цитат.";
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '➕ Добавить мысль', 'callback_data' => 'add_quote']],
                    [['text' => '🎲 Случайная мысль', 'callback_data' => 'random_quote']],
                ]
            ];
            $bot->SendMessage($chatId, $msg, $keyboard);
        }
    }
}