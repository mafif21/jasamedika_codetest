<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    public static function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'code' => $code,
        ];
        if (!empty($message)) {
            $response['message'] = $message;
        }

        if ($result != null) {
            $response['data'] = $result;
        }

        return response()->json($response, $code);
    }
}
