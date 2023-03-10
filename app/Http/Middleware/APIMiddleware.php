<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

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
        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($token);
        } catch (FailedToVerifyToken $e) {
            return ['status'=>401,'message'=>$e->getMessage()];
        }

        return ['status'=>200,'message'=>$verifiedIdToken->claims()->get('sub')];
    }
}
