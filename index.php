<?php
require_once __DIR__ . '/init.php';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!$data) {
    exit;
}

$update = new Update($data);

if (!$update->isValid()) {
    exit;
}

