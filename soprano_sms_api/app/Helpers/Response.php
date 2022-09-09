<?php

namespace App\Helpers;

class Response
{
    /**
     * Helper method to send response as json
     *
     * @param array $responseData
     * @param int $responseCode
     * @return void
     */
    public static function send(array $responseData, int $responseCode): void
    {
        header('Content-type: application/json; charset=utf-8');
        http_response_code($responseCode);
        echo json_encode($responseData);
    }
}
