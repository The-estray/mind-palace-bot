<?php
require_once __DIR__ . '/autoloader.php';

$config = require __DIR__ . '/src/config/db.php';

$database = new Database($config['host'], $config['dbname'], $config['user'], $config['password']);
$pdo = $database->getConn();

$userRepo = new UserRepository($pdo);
$quoteRepo = new QuoteRepository($pdo);
$categoryRepo = new CategoryRepository($pdo);

$bot = new Bot($config['token']);
