<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\Model\ConnectedDevice;
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
        $auth_token = '';
        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $auth->getUser($uid);
        if(!empty($user->phoneNumber)){
            $user_check = User::select('id','phone','firebase_auth_id','auth_access_token')->where(['phone'=>$user->phoneNumber])->first();
            if(empty($user_check->id)){
                $get_user = User::create([
                    'phone' => $user->phoneNumber,
                    'firebase_auth_id' => $uid
                ]);
                $auth_token = $this->auth_token($get_user->id,"");
            }else{
                if(!empty($user_check->id)){ //echo "<pre>"; print_r(); die;
                    $auth_token = $this->auth_token($user_check->id,$user_check->auth_access_token);
                }
            }

            if($auth_token != ''){
                return response()->json(['status'=>200,'phone'=>$user->phoneNumber,'auth_token'=>$auth_token,'message'=>'Success'],200);
            }else{
                return response()->json(['status'=>401,'message'=>'Token not Authorized'],401);
            }

        }else{
            return response()->json(['status'=>401,'message'=>'Token not Authorized'],401);
        }

    }

    public function auth_token($id, $old_token = "")
    {
        if ($old_token != "") {
            $token = $old_token;
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $token = $id . $token . $id;
        }

        $user = User::where('id', $id)->update(['auth_access_token' => $token]);

        if ($user) {
            return $token;
        } else {
            return false;
        }
    }

    public function logout(Request $request){
        $id = $request->id;
        User::where(['id'=>$id])->update(['auth_access_token'=>'']);
        return response()->json(['status'=>200,'message'=>'Successfully Logout'],200);
    }

    //START DEVICE API's

    public function connect_device(Request $request){
        $device_uuid = $request->uuid;
        $device_id = $request->device_id;
        $device_mac_id = $request->mac_id;
		$auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $check = ConnectedDevice::insert(['device_id'=>$device_id,'mac_id'=>$device_mac_id,'user_id'=>$user_details->id,'device_uuid'=>$device_uuid]);
            if($check){
                return response()->json(['status'=>200,'message'=>'Device connected successfully'],200);
            }
        }

        return response()->json(['status'=>400,'message'=>'Something Went Wrong, Please try again latter'],400);
    }

    public function edit_device(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function delete_device(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
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

    //END DEVICE API's

}
