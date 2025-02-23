<?php

namespace App;

trait ApiResponse
{
    protected function success_response($message = 'Success',$data = null ,$flags=[])
    {
 
        return $this->api_response(true,$message,$data,$flags);
    }
    protected function error_response($message = 'Error',$data = null,$flags=[])
    {
        return $this->api_response(false,$message,$data,$flags);
    }
    protected function api_response($status=true,$msg="",$data="",$flags=[]){
        return response()->json([
            'status'  => $status,
            'message' => $msg,
            'data'    => $data,
            'flags'=>$flags
        ]);
    }
}
