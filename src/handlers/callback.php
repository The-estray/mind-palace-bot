<?php
require_once __DIR__ . '/../init.php';

if (isset($data['callback_query'])) {
    $callbackId = $data['callback_query']['id'];
    $chatId     = $data['callback_query']['message']['chat']['id'];
    $messageId  = $data['callback_query']['message']['message_id'];
    $telegramId = $data['callback_query']['from']['id'];
    $action     = $data['callback_query']['data'];
    $bot->answerCallbackQuery($callbackId);
    $count = $quoteRepo->countByUserId($telegramId);

    match (true) {
        $action === 'random_quote' => handleRandomQuote($quoteRepo, $bot, $chatId, $telegramId, $messageId, $callbackId),
        $action === 'add_quote' => handleAddQuote($userRepo, $bot, $telegramId, $chatId),
        $action === 'cancel_action' => handleCancelAction($userRepo, $bot, $telegramId, $messageId, $chatId, $count),
        $action === 'new_category' => handleNewCategory($userRepo, $bot, $chatId, $telegramId, $messageId),
        $action === 'open_chambers' => handleOpenChambers($bot, $quoteRepo, $categoryRepo, $telegramId, $chatId, $messageId),
        str_starts_with($action, 'save_to_cat_') => handleSaveToCategory($bot, $userRepo, $quoteRepo, $messageId, $chatId, $telegramId, $action, $count),
        default => null,
    };
}
