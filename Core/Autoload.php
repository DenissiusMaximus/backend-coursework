<?php

spl_autoload_register(function (string $className) {
    $basePath = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    $filePath = $basePath . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
    if(file_exists($filePath)) {
        require_once $filePath;
        return;
    }

    $directories = [
        'Core/',
        'Core/Middleware/',
        'Models/',
        'Controllers/',
        'Views/',
        'Services/'
    ];

    foreach ($directories as $directory) {
        $filePath = $basePath . $directory . $className . '.php';
        
        if (file_exists($filePath)) {
            require_once $filePath;
            return; 
        }
    }
});