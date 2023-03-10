<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\User;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
class GeneralController extends Controller
{
    public function faq(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function get_pages($page_name = null){ // page type = terms_condition, privacy_policy, support, about_us
        if($page_name != null){
            $data = BusinessSetting::where('type', $page_name)->first();
            if(!empty($data)){
                return response()->json(['status'=>200,'message'=>'Success','data'=>$data],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Page not available'],200);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'Page not found'],400);
        }
    }

    //GET MOBILE NO. CHECK AND VERIFY INTO DB AND SEND IN RESPONSE
    public function verify_user(Request $request){
        $mobile = $request->mobile;
        $user = User::select('id','phone')->where(['phone'=>$mobile,'is_active'=>0])->first();
        if(!empty($user)){
            return response()->json(
                [
                    'status'=>400,
                    'message'=>'Not Activated'
                ]
            ,200);
        }else{
            return response()->json(
                [
                    'status'=>200,
                    'message'=>'Success'
                ]
            ,200);
        }

    }

    //GET FIREBASE AUTH TOKEN. CHECK AND VERIFY THEN ADD INTO DB AND SEND USER INFO IN RESPONSE
    public function user_authentication(Request $request){
        $token = $request->token;
        $auth = app('firebase.auth');
        try {
            $verifiedIdToken = $auth->verifyIdToken($token);
        } catch (FailedToVerifyToken $e) {
            return response()->json(['status'=>400,'message'=>$e->getMessage()],400);
        }
        
        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $auth->getUser($uid);
        if(!empty($user->phoneNumber)){
            $user_check = User::select('id','phone')->where(['phone'=>$user->phoneNumber,'is_active'=>1])->first();
            if(empty($user_check->id)){
                User::insert(['phone'=>$user->phoneNumber,'firebase_auth_id'=>$uid,'auth_access_token'=>$token]);
            }else{
                User::where(['phone'=>$user->phoneNumber,'firebase_auth_id'=>$uid])->update(['firebase_auth_id'=>$uid,'auth_access_token'=>$token]);
            }
            return response()->json(['status'=>200,'phone'=>$user->phoneNumber,'message'=>'Success'],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Something went wrong'],400);
        }
    }

    public function device_type_list(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function active_device_list(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function get_specific_device($device_id){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function previous_added_device_list(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function add_device(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function edit_device(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function delete_device(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    

}
