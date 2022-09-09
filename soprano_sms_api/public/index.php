<?php

/**
 * Application default page.
 * It just returns, using instructions of this app. 
 * Other files inside public directory handles their respective task.
 */

require __DIR__.'/../app/bootstrap.php';

new App\Controllers\IndexController();
