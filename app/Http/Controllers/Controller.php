<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\MessageBag;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static function success( $data ,  $message = '' , $code = 200 , $status = 200)
    {
        return response()->json(['code' => $code , 'status' => true ,'message' => $message , 'data' => $data] , $status)->header('Content-type','application/json');
    }

    public static function error($message , $data = NULL , $code = 400 , $status = 200)
    {
        $messageCount = 1;
        if (is_array($message))
            $messageCount = sizeof($messageCount);
        elseif ($message instanceof Controller){
            $messageCount =$message->count();
        }
        if ($message instanceof MessageBag)
            $message= $message->first();
        return response()->json(['code' => $code , 'status' => false , 'message' => $message ,'data'=> $data], $status)->header('Content-type','application/json');
    }
}
