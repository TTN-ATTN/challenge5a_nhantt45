<?php
spl_autoload_register(function ($class) {
    $preFix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($preFix);
    if (strncmp($preFix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});