<?php
file_put_contents('raw_log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);
require_once __DIR__ . '/init.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

