<?php
/*
* Workday - A time clock application for employees
* Support: official.codefactor@gmail.com
* Version: 1.6
* Author: Brian Luna
* Copyright 2020 Codefactor
*/
namespace App\Http\Middleware;
use Auth;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\Response;
use App\Enums\Role as RoleType;

class Admin
{
    use Response;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function __construct()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function handle($request, Closure $next)
    {
        $t = $this->user->role;
        
        if ($t == RoleType::Doctor) 
        {
            return true;
        } else {
            return false;
        }

        return $next($request);
    }
}
