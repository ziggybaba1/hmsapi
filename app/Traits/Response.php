<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;



trait Response {


    public function successResponse(int $statusCode, string $message, $data = NULL) : JsonResponse {

        return response()->json(["data" => $data, "status"=> true,"message" => $message],$statusCode);

    }

    public function failureResponse(int $statusCode, string $message) : JsonResponse {

        return response()->json(["status"=> false,"message" => $message],$statusCode);

    }

    
}