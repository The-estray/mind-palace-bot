<?php

function handleWaitingQuote(
    UserRepository $userRepo,
    Bot $bot,
    CategoryRepository $categoryRepo,
    string $text,
    int $telegramId,
    int $chatId,
) {
    if (empty($text)) {
        $bot->SendMessage($chatId, 'Чертоги принимают только текстовые мысли. Попробуй еще раз:');
        return;
    }

    $userRepo->setTempText($telegramId, $text);
    $userRepo->setStep($telegramId, 'waiting_category_choice');

    $cats = $categoryRepo->getAllByUserId($telegramId);
    $keyboard = Keyboard::chooseCategory($cats);

    $bot->SendMessage($chatId, 'В какую папку положить эту мысль?', $keyboard);
}

function handleForNewCategory(
    Bot $bot,
    CategoryRepository $categoryRepo,
    UserRepository $userRepo,
    QuoteRepository $quoteRepo,
    int $chatId,
    int $telegramId,
    string $text,
) {
    if (empty($text)) {
        $bot->SendMessage($chatId, 'Ты не можешь оставить название категории пустым!');
        return;
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
}

function handleStart(
    Bot $bot,
    QuoteRepository $quoteRepo,
    int $telegramId,
    int $chatId,
) {
    $count = $quoteRepo->countByUserId($telegramId);
    $msg = $count === 0
        ? 'Чертоги пока пусты.'
        : "С возвращением. В чертогах сохранено {$count} цитат.";
    $bot->SendMessage($chatId, $msg, Keyboard::start($count));
}
