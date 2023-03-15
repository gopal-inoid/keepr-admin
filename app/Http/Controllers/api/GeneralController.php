<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\Model\ConnectedDevice;
use App\Model\Banner;
use App\Model\Product;
use App\Model\Order;
use App\User;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
class GeneralController extends Controller
{
    public function get_pages(Request $request){ // page type = terms_condition, privacy_policy, support, about_us
        //echo ' - ' . $request->page_name; die;
        echo "<pre>"; print_r($_GET); die;
        if($request->page_name){
            $data = BusinessSetting::where('type', $request->page_name)->first();
            if(!empty($data)){
                return response()->json(['status'=>200,'message'=>'Success','data'=>$data],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Page not available'],200);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'Page not found'],400);
        }
    }

    public function get_banners(){
        $banners_list = Banner::where(['published'=>1])->get();
        if(!empty($banners_list)){
            return response()->json(['status'=>200,'message'=>'Success','data'=>$banners_list],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Banners not found'],400);
        }
    }

    //START USER AUTH API's

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
        try {
            $auth = app('firebase.auth');
            //echo "<pre>"; print_r($auth); die;
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

    //END USER AUTH API's

    //START USER API's
    public function delete_user_account(Request $request){
		$auth_token   = $request->headers->get('X-Access-Token');
        $user = User::where(['auth_token'=>$auth_token])->first();
        if(!empty($user->id)){
            ConnectedDevice::where(['user_id'=>$user->id])->delete();
            $deleted = $user->delete();
            if(!empty($deleted)){
                return response()->json(['status'=>200,'message'=>'User successfully deleted'],200);
            }else{
                return response()->json(['status'=>400,'message'=>'User not deleted'],400);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function user_profile(Request $request){
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $all_data['phone'] = $user_details->phone;
            $get_orders = Order::where(['customer_id'=>$user_details->id])->get();
            if(!empty($get_orders)){
                $all_data['order_list'] = $get_orders;
            }
            return response()->json(['status'=>200,'message'=>'Success','data'=>$all_data],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    //END USER API's

}
