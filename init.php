<?php
require_once 'config/db.php';
require_once 'core/bot.php';

require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/QuoteRepository.php';

$userRepo = new UserRepository($pdo);
$quoteRepo = new QuoteRepository($pdo);

$bot = new Bot($token);
