<?php
namespace App\Traits;
use DB;
use App\Classes\Table;
use JWTAuth;

trait LoggedTrait {


    public static function Logged() {
        try {
            if ( JWTAuth::parseToken()->authenticate()) {
                return true;
            }

        }
           catch (\Exception $e) {
                    if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                        return false;
                    }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                        return false;
                    } else if ( $e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                        return false;
                    }else{
                        return false;
                    }
                }
    }
  
}