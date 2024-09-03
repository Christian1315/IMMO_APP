<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class BASE_HELPER extends Controller
{
   
    ##========== RESPONSE MANAGEMENT =======##
    static function sendResponse($data,$message) {
        $response = [
            'status'=>True,
            'data'=>$data,
            'message'=>$message,
        ];

        return response()->json($response,200);
    }

    static function sendError($message,$code) {
        $response = [
            'status'=>false,
            'erros'=>$message,
        ];

        return response()->json($response,$code);
    } 
    
    #======= METHOD VALIDATION =====#
    function methodValidation($method,$supportedMethod){
        $method_validated=False;
        if($method==$supportedMethod){
            $method_validated = true;
        }
        return $method_validated;
    } 
}