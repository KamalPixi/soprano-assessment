<?php

ini_set('display_errors', 1);

// Composer auto loader
require __DIR__.'/../vendor/autoload.php';

// Add config
require_once __DIR__.'/config.php';

// Define global exception handler
function handleException($exception): void
{
    App\Helpers\Response::send(
        [
            'success' => false,
            'message' => $exception->getMessage(),
            'data' => []
        ],
        200
    );

    exit;
}

// Set exception handler
set_exception_handler('handleException');
