<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\User;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
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

    // //GET MOBILE NO. INSERT INTO DB AND SEND OTP IN RESPONSE
    // public function get_otp(Request $request){
    //     $mobile = $request->mobile;
    //     $key = random_int(0, 999999);
    //     $rand_otp = str_pad($key, 6, 0, STR_PAD_LEFT);
    //     $user = User::select('id','phone','otp_code')->where('phone',$mobile)->first();
    //     if(!empty($user)){
    //         $user->otp_code = $rand_otp;
    //         $user->save();
    //         return response()->json(
    //             [
    //                 'status'=>200,
    //                 'code'=>$rand_otp,
    //                 'phone'=>$mobile,
    //                 'message'=>'success'
    //             ]
    //         ,200);
    //     }else{
    //         $user = User::insert(['phone'=>$mobile,'otp_code'=>$rand_otp]);
    //         if($user){
    //             return response()->json(
    //                 [
    //                     'status'=>200,
    //                     'code'=>$rand_otp,
    //                     'phone'=>$mobile,
    //                     'message'=>'success'
    //                 ]
    //             ,200);
    //         }
    //     }

    //     return response()->json(
    //         [
    //             'status'=>400,
    //             'message'=>'something went wrong!'
    //         ]
    //     ,400);

    // }

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
        
    }

    public function device_type_list(){
        return response()->json(HelpTopic::orderBy('ranking')->get(),200);
    }

    public function active_device_list(){
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
