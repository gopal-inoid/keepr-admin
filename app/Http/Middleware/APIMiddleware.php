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
        $platform   = $request->headers->get('x-platform');
        $api_version   = $request->headers->get('x-app-version');
        if(empty($api_version)){
            return response()->json(['status'=>406,'message'=>'Need to Pass App Version.'],406);
        }
        $check = $this->check_force_update($platform,$api_version);
        if(isset($check['status']) && $check['status'] == 406)
        {
            return response()->json(['status'=>406,'force_update'=>"1",'message'=> !empty($check['message']) ? $check['message'] : "Need to Update" ],406);
        }
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

    public function check_force_update($platform,$api_version){
        $check = \DB::table('api_versions')->where('platform',$platform)->first();
        if(!empty($check->id)){
            if($check->status == 1 || $api_version > $check->version){
                return ['status'=>406,'message'=>$check->message];
            }else{
                return [];
            }
        }else{
            return [];
        }
    }
    
}
