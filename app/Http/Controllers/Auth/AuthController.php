<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Traits\Response as Callback;
use DB;
use App\Models\User;
use App\Classes\Table;

class AuthController extends Controller
{
    use Callback;
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        //valid credential
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        //Send failed response if request is not valid
            if($validator->fails()) return $this->failureResponse(401,$validator->errors()->first());
            // $request=$this->reset($request->all());
           
        //Request is validated
        //Crean token
        if (! $token = auth()->attempt(['email'=>$request->email,'password'=>$request->password])) {
            return $this->failureResponse(401,'Unauthorized.');
            
        }
        
        try {
            if (! $token = JWTAuth::attempt(['email'=>$request->email,'password'=>$request->password])) {
                return $this->failureResponse(400,'Login credentials are invalid.');
            }
        } catch (JWTException $e) {
    	// return $credentials;
        return $this->failureResponse(500,'Could not create token.');
        }
        	//Token created, return with success response and jwt token
        $returnStatus=[
            'token'=>$token,
        ];
 	return $this->successResponse(200,'success',$returnStatus);
    }

    public function getAuthenticatedUser()
    {
            try {

            if ( $user = JWTAuth::parseToken()->authenticate()) {
                $returnStatus=[
                    'user'=>$user,
                ];
                return $this->successResponse(200,'success',$returnStatus);
                          
                    }

            } catch (\Exception $e) {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return $this->failureResponse(404,'user not found');
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return $this->failureResponse(404,'user not found');
                } else if ( $e instanceof \Tymon\JWTAuth\Exceptions\JWTException) {
                    return $this->failureResponse(404,'user not found');
                }else{
                    return $this->failureResponse(404,'user not found');
                }
            }
          
    }

    public function logout() 
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->successResponse(200,'Logout successfull','');
    }

    public function getAllUserInfo()
    {
        try {
        if (! JWTAuth::parseToken()->authenticate()) {
            return $this->failureResponse(401,'denied');
        }
        $data = table::users()
        ->select('users.id', 'users.uuid', 'users.name', 'users.email','user_details.*','user_accounts.*')
		->join('user_details', 'user_details.user_id', '=', 'users.id')
		->join('user_accounts', 'user_accounts.user_id', '=', 'users.id')
		->paginate(30);
        return $this->successResponse(200,'success',$data);
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
