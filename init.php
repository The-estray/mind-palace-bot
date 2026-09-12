<?php
require_once 'config/db.php';
require_once 'core/bot.php';

// https://api.telegram.org/bot8844200419:AAFkf5zRtyNbgyoj5H2Mbm1_K3KMggaWJ_
// s/setWebhook?url=https://lumber-backlit-chevron.ngrok-free.dev/index.php

$host = '127.0.1.16';
$dbname = 'mind_palace';
$user = 'root';
$password = '';

$database = new Database($host,$dbname,$user,$password);
$pdo = $database->getConn();

$token = '8844200419:AAFkf5zRtyNbgyoj5H2Mbm1_K3KMggaWJ_s';

$bot = new Bot($token);
