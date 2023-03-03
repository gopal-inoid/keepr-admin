<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\HelpTopic;
use App\Model\BusinessSetting;

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
