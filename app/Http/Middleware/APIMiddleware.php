<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
class APIMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //$headers      = apache_request_headers();
        $auth_token   = $request->headers->get('X-Access-Token');
        if(empty($auth_token)){
            return response()->json(['status'=>401,'message'=>'Not Authorized.'],401);
        }
        $verify = $this->check_token($auth_token);
        if(isset($verify['status']) && $verify['status'] == 401)
        {
            header('Content-type: application/json');
            header('HTTP/1.1 403 Unauthorized', true, 403);
            return response()->json(['status'=>401,'message'=>'Auth token has been expired.'],401);
        }
        else
        {
            return $next($request);
        }

    }

    public function check_token($token){
        $user_check = User::select('firebase_auth_id')->where(['auth_access_token'=>$token])->first();
        if(!empty($user_check->firebase_auth_id)){
            return ['status'=>200,'message'=>$user_check->firebase_auth_id];
        }else{
            return ['status'=>401,'message'=>'Auth token has been expired.'];
        }
    }
}
