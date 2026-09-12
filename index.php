<?php
file_put_contents('raw_log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
require_once 'init.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (isset($data['message'])) {
    $chatId     = $data['message']['chat']['id'];
    $telegramId = $data['message']['from']['id'];
    $text       = trim($data['message']['text'] ?? '');
    $firstName  = $data['message']['from']['first_name'] ?? '';
    $username   = $data['message']['from']['username'] ?? null;

    $sql = "INSERT INTO users (telegram_id,first_name,username) VALUES (?,?,?) 
    ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), username = VALUES(username)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$telegramId, $firstName, $username]);

    $sql = "SELECT step FROM users WHERE telegram_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$telegramId]);
    $step = $stmt->fetchColumn();

    if ($step === 'waiting_quote') {

        if (empty($text)) {
            $bot->SendMessage($chatId, 'Чертоги принимают только текстовые мысли. Попробуй еще раз:');
            exit;
        }

        $sql = "INSERT INTO quotes (user_id, text) VALUES (?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$telegramId, $text]);

        $bot->SendMessage($chatId, 'Мысль зафиксирована в Чертогах.');

        $sql = "UPDATE users set step = NULL WHERE telegram_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$telegramId]);
        exit;
    }

    if ($text === '/start') {
        $sql = "SELECT COUNT(*) FROM quotes WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$telegramId]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            $newText = 'Чертоги пока пусты.';
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '➕ Добавить первую мысль', 'callback_data' => 'add_quote']],
                ]
            ];
            $bot->SendMessage($chatId, $newText, $keyboard);
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
} elseif (isset($data['callback_query'])) {
    $callbackId = $data['callback_query']['id'];
    $chatId     = $data['callback_query']['message']['chat']['id'];
    $messageId  = $data['callback_query']['message']['message_id'];
    $telegramId = $data['callback_query']['from']['id'];
    $action     = $data['callback_query']['data'];

    if ($action === 'random_quote') {
        $bot->answerCallbackQuery($callbackId);

        $sql = "SELECT text FROM quotes WHERE user_id = ? ORDER BY RAND() LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$telegramId]);
        $quote = $stmt->fetch();

        if ($quote) {
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🎲 Еще мысль', 'callback_data' => 'random_quote']],
                    [['text' => '➕ Добавить мысль', 'callback_data' => 'add_quote']],
                ]
            ];

            $bot->editMessageText($chatId, $messageId, $quote['text'], $keyboard);
        } else {
            $bot->answerCallbackQuery($callbackId, 'В Чертогах пока пусто!');
        }
    } elseif ($action === 'add_quote') {
        $bot->answerCallbackQuery($callbackId);

        $sql = "UPDATE users SET step = 'waiting_quote' WHERE telegram_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$telegramId]);
        $bot->SendMessage($chatId, 'Напиши и отправь свою мысль прямо сюда. Я зафиксирую её в Чертогах.');
    }
}
