<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\CPU\Helpers;
use App\Model\ConnectedDevice;
use App\Model\Banner;
use App\Model\Product;
use App\Model\Order;
use App\Model\DeviceRequest;
use App\User;
use Illuminate\Support\Facades\Http;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;
class GeneralController extends Controller
{
    public function get_pages(Request $request){ // page type = terms_condition, privacy_policy, support, about_us,faq
        if($request->page_name){
            if($request->page_name == 'faq'){
                $data = HelpTopic::select('question','answer')->where('status',1)->get();
            }else{
                $data = BusinessSetting::where('type', $request->page_name)->first();
            }
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
        $user = User::select('id','phone','is_active')->where(['phone'=>$mobile])->first();
        if(!empty($user->id)){
            if($user->is_active != 1){
                return response()->json(['status'=>400,'message'=>'Not Activated'],200);
            }else{
                return response()->json(['status'=>200,'message'=>'Success'],200);
            }
        }else{
            return response()->json(['status'=>200,'message'=>'Success'],200);
        }
    }

    //GET FIREBASE AUTH TOKEN. CHECK AND VERIFY THEN ADD INTO DB AND SEND USER INFO IN RESPONSE
    public function user_authentication(Request $request){
        $token = $request->token;
        $fcm_token = $request->fcm_token;
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
                    'firebase_auth_id' => $uid,
                    'fcm_token' => $fcm_token
                ]);
                $auth_token = $this->auth_token($get_user->id,"");
            }else{
                if(!empty($user_check->id)){ //echo "<pre>"; print_r(); die;
                    $auth_token = $this->auth_token($user_check->id,$user_check->auth_access_token,$fcm_token);
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

    public function auth_token($id, $old_token = "",$fcm_token = "")
    {
        if ($old_token != "") {
            $token = $old_token;
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $token = $id . $token . $id;
        }

        $user = User::where('id', $id)->update(['auth_access_token' => $token,'fcm_token'=>$fcm_token]);

        if ($user) {
            return $token;
        } else {
            return false;
        }
    }

    public function logout(Request $request){
        $auth_token   = $request->headers->get('X-Access-Token');
        $user = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user->id)){
            User::where(['id'=>$user->id])->update(['auth_access_token'=>'']);
            return response()->json(['status'=>200,'message'=>'Successfully Logout'],200);
        }else{
            return response()->json(['status'=>200,'message'=>'User not found'],200);
        }
    }

    //END USER AUTH API's

    //START USER API's
    public function delete_user_account(Request $request){
		$auth_token   = $request->headers->get('X-Access-Token');
        $user = User::where(['auth_access_token'=>$auth_token])->first();
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
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $all_data['phone'] = $user_details->phone;
            $get_orders = Order::select('payment_status','order_status','order_amount','shipping_address','billing_address')->where(['customer_id'=>$user_details->id])->get();
            if(!empty($get_orders)){
                $all_data['order_list'] = $get_orders;
            }
            return response()->json(['status'=>200,'message'=>'Success','data'=>$all_data],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function order_detail(Request $request){
        $order_id = $request->order_id;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $get_orders = Order::where(['id'=>$order_id])->first();
            if(!empty($get_orders->id)){
                return response()->json(['status'=>200,'message'=>'Success','data'=>$get_orders],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Order not found'],400);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function add_address(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ], [
            'phone.required' => 'Phone is required!'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $user_details->street_address = $request->address;
            $user_details->name = $request->name;
            $user_details->email = $request->email;
            $user_details->phone = $request->phone;
            $user_details->country = $request->country;
            $user_details->city = $request->city;
            $user_details->state = $request->state;
            $user_details->zip = $request->zip_code;
            $user_details->save();
            return response()->json(['status'=>200,'message'=>'Success'],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function add_shipping_address(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ], [
            'phone.required' => 'Phone is required!'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $address = $request->address;
        $is_billing_same = $request->is_billing_same;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            if($is_billing_same === 'true'){
                $user_details->is_billing_address_same = 1;
                $user_details->add_shipping_address = $user_details->street_address;
            }else{
                $user_details->add_shipping_address = $address;
                $user_details->is_billing_address_same = 0; 
            }

            $user_details->shipping_name = $request->name;
            $user_details->shipping_email = $request->email;
            $user_details->shipping_phone = $request->phone;
            $user_details->shipping_country = $request->country;
            $user_details->shipping_city = $request->city;
            $user_details->shipping_state = $request->state;
            $user_details->shipping_zip = $request->zip_code;

            $user_details->save();
            return response()->json(['status'=>200,'message'=>'Success'],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function edit_address(Request $request){
        $type = $request->type;
        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ], [
            'phone.required' => 'Phone is required!'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        
        if(!empty($user_details->id)){

            if($type == 'shipping'){
                if($request->is_billing_same === 'true'){
                    $user_details->is_billing_address_same = 1;
                    $user_details->add_shipping_address = $user_details->street_address;
                    $user_details->shipping_name = $user_details->name;
                    $user_details->shipping_email = $user_details->email;
                    $user_details->shipping_phone = $user_details->phone;
                    $user_details->shipping_country = $user_details->country;
                    $user_details->shipping_city = $user_details->city;
                    $user_details->shipping_state = $user_details->state;
                    $user_details->shipping_zip = $user_details->zip;
                }else{
                    $user_details->add_shipping_address = $request->address;
                    $user_details->is_billing_address_same = 0;
                    $user_details->shipping_name = $request->name;
                    $user_details->shipping_email = $request->email;
                    $user_details->shipping_phone = $request->phone;
                    $user_details->shipping_country = $request->country;
                    $user_details->shipping_city = $request->city;
                    $user_details->shipping_state = $request->state;
                    $user_details->shipping_zip = $request->zip;
                }
            }else{
                $user_details->street_address = $request->address;
                $user_details->name = $request->name;
                $user_details->email = $request->email;
                $user_details->phone = $request->phone;
                $user_details->country = $request->country;
                $user_details->city = $request->city;
                $user_details->state = $request->state;
                $user_details->zip = $request->zip_code;
            }

            $user_details->save();
            return response()->json(['status'=>200,'message'=>'Address successfully updated'],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function get_address(Request $request){
        $type = $request->type;
        
        // $validator = Validator::make($request->all(), [
        //     'type' => 'required'
        // ], [
        //     'type.required' => 'Type is required!'
        // ]);

        // if ($validator->errors()->count() > 0) {
        //     return response()->json(['errors' => Helpers::error_processor($validator)]);
        // }

        $address = [];
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            if($type == 'shipping'){
                $address['address'] = $user_details->add_shipping_address;
                $address['isSameBillingAdd'] = $user_details->is_billing_address_same;
                $address['name'] = $user_details->shipping_name;
                $address['email'] = $user_details->shipping_email;
                $address['phone'] = $user_details->shipping_phone;
                $address['country'] = $user_details->shipping_country;
                $address['city'] = $user_details->shipping_city;
                $address['state'] = $user_details->shipping_state;
                $address['zip'] = $user_details->shipping_zip;
            }else{
                $address['address'] = $user_details->street_address;
                $address['name'] = $user_details->name;
                $address['email'] = $user_details->email;
                $address['phone'] = $user_details->phone;
                $address['country'] = $user_details->country;
                $address['city'] = $user_details->city;
                $address['state'] = $user_details->state;
                $address['zip'] = $user_details->zip;
            }

            return response()->json(['status'=>200,'message'=>'Success','data'=>$address],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function order_history(Request $request){
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $get_orders = Order::select('customer_id','payment_status','order_status','payment_method','order_amount','shipping_address','created_at','coupon_code')->where(['customer_id'=>$user_details->id])->get();
            return response()->json(['status'=>200,'message'=>'Success','data'=>$get_orders],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    //END USER API's

}
