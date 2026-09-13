<?php
require_once __DIR__ . '/init.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    exit;
}

if (isset($data['message'])) {
    require_once __DIR__ . '/handlers/message.php';
} elseif (isset($data['callback_query'])) {
    require_once __DIR__ . '/handlers/callback.php';
}
