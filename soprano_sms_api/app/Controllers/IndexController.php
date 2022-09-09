<?php

namespace App\Controllers;

use App\Helpers\Response;


/*
 * Default controller for application root
 * Returns a json that contains steps of how to use this api.
 */
class IndexController 
{
    public function __construct()
    {
        $this->welcomeMessage();
    }

    public function welcomeMessage(): void
    {
        Response::send(
            [
                'success' => true,
                'message' => 'API using instructions',
                'data' => [
                    'insert_in_queue' => [
                        'endpoint' => $_SERVER['HTTP_HOST'] . '/' . 'queues.php',
                        'method' => 'POST',
                        'data_structure' => '{"to":"601111085061", "message":"Hello Soprano"}',
                    ],
                    'get_all_messages' => [
                        'endpoint' =>  $_SERVER['HTTP_HOST'] . '/' . 'queues.php',
                        'method' => 'GET',
                    ],
                    'get_single_message' => [
                        'endpoint' =>  $_SERVER['HTTP_HOST'] . '/' . 'queues_single.php',
                        'method' => 'GET',
                    ],
                    'get_queue_size' => [
                        'endpoint' =>  $_SERVER['HTTP_HOST'] . '/' . 'queues_total.php',
                        'method' => 'GET',
                    ]
                ],
            ],
            200
        );
    }
}
