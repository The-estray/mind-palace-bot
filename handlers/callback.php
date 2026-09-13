<?php
require_once __DIR__ . '/../init.php';

if (isset($data['callback_query'])) {
    $callbackId = $data['callback_query']['id'];
    $chatId     = $data['callback_query']['message']['chat']['id'];
    $messageId  = $data['callback_query']['message']['message_id'];
    $telegramId = $data['callback_query']['from']['id'];
    $action     = $data['callback_query']['data'];
    $bot->answerCallbackQuery($callbackId);

    if ($action === 'random_quote') {
        $quote = $quoteRepo->getRandomByUserId($telegramId);

        if ($quote !== null) {
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
    } elseif ($action === 'add_quote') {
        $userRepo->setStep($telegramId, 'waiting_quote');
        $bot->SendMessage($chatId, 'Напиши и отправь свою мысль прямо сюда. Я зафиксирую её в Чертогах.');
    } elseif (str_starts_with($action, 'save_to_cat_')) {
        $categoryId = (int)str_replace('save_to_cat_', '', $action);
        $tempText = $userRepo->getTempText($telegramId);

        $catId = $categoryId === 0 ? null : $categoryId;
        if ($tempText !== null) {
            $quoteRepo->create($telegramId, $tempText, $catId);
        }

        $userRepo->setTempText($telegramId, null);
        $userRepo->setStep($telegramId, null);
        $bot->editMessageText($chatId, $messageId, 'Мысль успешно сохранена!');
    } elseif ($action === 'new_category') {
        $userRepo->setStep($telegramId, 'new_category');
        $bot->editMessageText($chatId, $messageId, 'Напиши название для новой папки:');
    }
}
