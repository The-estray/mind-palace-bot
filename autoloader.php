<?php

function my_autoloader(string $class) {
    $dirs = [__DIR__ . '/src/Core/', __DIR__ . '/src/Repositories/',__DIR__ . '/src/Helpers/', __DIR__ . '/src/Controllers/'];

    foreach ($dirs as $dir) {
        $path = $dir . $class . '.php';
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

spl_autoload_register('my_autoloader');