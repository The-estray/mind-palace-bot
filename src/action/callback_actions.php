<?php

function handleRandomQuote(
    QuoteRepository $quoteRepo,
    Bot $bot, 
    int $chatId, 
    int $telegramId, 
    int $messageId, 
    string $callbackId,
) {
    $quote = $quoteRepo->getRandomByUserId($telegramId);

    if (!empty($quote)) {
        $keyboard = Keyboard::randomQuote();
        $bot->editMessageText($chatId, $messageId, $quote, $keyboard);
    } else {
        $bot->answerCallbackQuery($callbackId, 'В чертогах пока пусто.');
    }
}

function handleAddQuote(
    UserRepository $userRepo,
    Bot $bot,
    int $telegramId,
    int $chatId,
) {
    $userRepo->setStep($telegramId, 'waiting_quote');
    $bot->SendMessage($chatId, 'Напиши и отправь свою мысль прямо сюда. Я зафиксирую её в Чертогах.', Keyboard::cancelAction());
}

function handleCancelAction(
    UserRepository $userRepo,
    Bot $bot,
    int $telegramId,
    int $messageId,
    int $chatId,
    int $count,
) {
    $userRepo->setStep($telegramId, null);
    $userRepo->setTempText($telegramId, null);
    $bot->editMessageText($chatId,$messageId, 'Действие отменено.', Keyboard::start($count));
}

function handleNewCategory(
    UserRepository $userRepo,
    Bot $bot,
    int $chatId,
    int $telegramId,
    int $messageId,
) {
    $userRepo->setStep($telegramId, 'new_category');
    $bot->editMessageText($chatId, $messageId, 'Напиши название для новой папки:');
}

function handleSaveToCategory(
    Bot $bot,
    UserRepository $userRepo,
    QuoteRepository $quoteRepo,
    int $messageId,
    int $chatId,
    int $telegramId,
    string $action,
    int $count, 
) {
    $categoryId = (int)str_replace('save_to_cat_', '', $action);
    $tempText = $userRepo->getTempText($telegramId);
    $catId = $categoryId === 0 ? null : $categoryId;

    if ($tempText !== null) {
        $quoteRepo->create($telegramId, $tempText, $catId);
    } 

    $userRepo->setTempText($telegramId, null);
    $userRepo->setStep($telegramId, null);
    $bot->editMessageText($chatId, $messageId, 'Мысль успешно сохранена!', Keyboard::start($count));
}

function handleOpenChambers(
    Bot $bot,
    QuoteRepository $quoteRepo,
    CategoryRepository $categoryRepo,
    int $telegramId,
    int $chatId,
    int $messageId,


) {
    $cats = $categoryRepo->getAllWithQuotesCountByUserId($telegramId);
    $countWithoutCat = $quoteRepo->countWithoutCategory($telegramId);
    $bot->editMessageText($chatId, $messageId, "🏛 Твои Чертоги разума:\n\nВыбери папку для просмотра мыслей:", Keyboard::openChambers($cats, $countWithoutCat));
}