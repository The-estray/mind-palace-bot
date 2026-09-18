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

    match (true) {
        $step === 'waiting_quote' => handleWaitingQuote($userRepo, $bot, $categoryRepo, $text, $telegramId, $chatId),
        $step === 'new_category' => handleForNewCategory($bot, $categoryRepo, $userRepo, $quoteRepo, $chatId, $telegramId, $text),
        $text === '/start' => handleStart($bot, $quoteRepo, $telegramId, $chatId),
        default => null,
    };
}
