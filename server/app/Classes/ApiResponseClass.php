<?php

namespace App\Classes;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ApiResponseClass
{
    public function __construct()
    {
        //
    }

    public static function rollback($e, $message = "Something went wrong! Process not completed")
    {
        DB::rollBack();
        self::throw($e, $message);
    }

    public static function throw($e, $message = "Something went wrong! Process not completed")
    {
        Log::info($e);
        throw new HttpResponseException(response()->json(["message" => $message], 500));
    }

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
