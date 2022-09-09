<?php

/**
 * Queue Endpoint.
 * Responsible send/fetch messages from/to RabbitMQ
 */

require __DIR__.'/../app/bootstrap.php';;

new App\Controllers\QueueController(
    \App\Helpers\Validator::filter(
        basename($_SERVER['PHP_SELF'])
    )
);

