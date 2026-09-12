<?php
require_once 'config/db.php';
require_once 'core/bot.php';

require_once __DIR__ . '/repositories/UserRepository.php';
require_once __DIR__ . '/repositories/QuoteRepository.php';
require_once __DIR__ . '/repositories/CategoryRepository.php';

$userRepo = new UserRepository($pdo);
$quoteRepo = new QuoteRepository($pdo);
$categoryRepo = new CategoryRepository($pdo);

$bot = new Bot($token);
