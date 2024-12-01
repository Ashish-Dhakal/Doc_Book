<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function successResponse($result, $message)
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
        ], 200);
    }

    public function errorResponse($error , $errorMessage, $code = 404){
        return response()->json([
            'success' => false,
            'message' => $error,
            'reason' => $errorMessage,
        ], $code);
    }

}
