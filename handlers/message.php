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

    if (str_starts_with($text, '/')) {
        $userRepo->setStep($telegramId, null);
        $userRepo->setTempText($telegramId, null);
        $step = null;
    }

    if ($step === 'waiting_quote') {

        if (empty($text)) {
            $bot->SendMessage($chatId, 'Чертоги принимают только текстовые мысли. Попробуй еще раз:');
            exit;
        }

        $userRepo->setTempText($telegramId, $text);
        $userRepo->setStep($telegramId, 'waiting_category_choice');

        $cats = $categoryRepo->getAllByUserId($telegramId);
        $keyboard = [
            'inline_keyboard' => []
        ];

        foreach ($cats as $cat) {
            $keyboard['inline_keyboard'][] = [
                ['text' => '📁 ' . $cat['title'], 'callback_data' => 'save_to_cat_' . $cat['id']]
            ];
        }

        $keyboard['inline_keyboard'][] = [
            ['text' => '➕ Новая папка', 'callback_data' => 'new_category'],
            ['text' => '📥 Без папки', 'callback_data' => 'save_to_cat_0']
        ];

        $bot->SendMessage($chatId, 'В какую папку положить эту мысль?', $keyboard);

        // $quoteRepo->create($telegramId, $text);
        // $bot->SendMessage($chatId, 'Мысль зафиксирована в Чертогах.');
        // $userRepo->setStep($telegramId, null);
        exit;
    }

    if ($step === 'new_category') {
        if (empty($text)) {
            $bot->SendMessage($chatId, 'Ты не можешь оставить название категории пустым!');
            exit;
        }

        $newCatId = $categoryRepo->create($telegramId, $text);
        $tempText = $userRepo->getTempText($telegramId);

        if ($tempText !== null) {
            $quoteRepo->create($telegramId, $tempText, $newCatId);
            $userRepo->setStep($telegramId, null);
            $userRepo->setTempText($telegramId, null);
            $bot->SendMessage($chatId, "Папка {$text} создана, и мысль сохранена в неё!");
        } else {
            $userRepo->setStep($telegramId, null);
            $bot->SendMessage($chatId, "Папка {$text} успешно создана!");
        }
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
        } else {
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
