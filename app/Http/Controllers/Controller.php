<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Mail;
use App\Model\EmailTemplates;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        try {
            Helpers::currency_load();
        }catch (\Exception $exception){

        }
    }

    public function getCountryName($id){
        $country_names = \DB::table('country')->select('name')->where('id',$id)->first();
        return $country_names->name ?? "";
    }

    public function getStateName($id){
        $state_names = \DB::table('states')->select('name')->where('id',$id)->first();
        return $state_names->name ?? "";
    }

    public function getEmailTemplate($key){
        $get_email = EmailTemplates::select('id','subject','body')->where('status',1)->where('keys',$key)->first();
        if(!empty($get_email->id)){
            return $get_email;
        }else{
            return false;
        }
    }

    public function sendEmail($to,$subject,$body){
        $emailServices_smtp = Helpers::get_business_settings('mail_config');
        if ($emailServices_smtp['status'] == 1) {
            try{
                Mail::to($to)->send(new \App\Mail\TestEmailSender($subject, $body));
            }catch(\Exception $e){
                $error = $e->getMessage();
            }
            if(isset($error)){
                return ['status'=>2,'error'=>$error];
            }else{
                return ['status'=>1];
            }
        }
        return false;
    }

    public function replacedEmailVariables($status,$body){
        $notif_keys = ["{status}"];
        $notif_values   = [$status];
        $body = str_replace($notif_keys, $notif_values, $body);
        return $body;
    }

    public function getTaxCalculation($amount,$country_name,$state_name){
        $taxes = \DB::table('tax_calculation')->select('tax_amt','type')->where('country',$country_name)->first();
        $tax_calculation = [];
        if(!empty($taxes)){
            $tax_rates = json_decode($taxes->tax_amt,true);
            if($taxes->type == "fixed"){
                if($tax_rates[0]['tax1'] != ""){
                    $tax_amt = (($amount * $tax_rates[0]['tax1']) / 100);
                    $tax_calculation[0]['title'] = $tax_rates[0]['tax1'] . "% " . $tax_rates[0]['tax_txt1'];
                    $tax_calculation[0]['amount'] = number_format($tax_amt,2);
                    $tax_calculation[0]['percent'] = $tax_rates[0]['tax1'];
                }else{
                    return [];
                }
            }else{
                //echo "<pre>"; print_r($tax_rates); die;
                foreach($tax_rates as $taxval){
                    if($state_name == $taxval['state']){
                        $tax_amt1 = (($amount * $taxval['tax1']) / 100);
                        $tax_calculation[0]['title'] = $taxval['tax1'] . "% " . $taxval['tax_txt1'];
                        $tax_calculation[0]['amount'] = number_format($tax_amt1,2);
                        $tax_calculation[0]['percent'] = number_format($taxval['tax1'],3);
                        
                        if(!empty($taxval['tax2'])){
                            $tax_amt2 = (($amount * $taxval['tax2']) / 100);
                            $tax_calculation[1]['title'] = $taxval['tax2'] . "% " . $taxval['tax_txt2'];
                            $tax_calculation[1]['amount'] = number_format($tax_amt2,2);
                            $tax_calculation[1]['percent'] = number_format($taxval['tax2'],3);
                        }
                    }
                }
            }

            return $tax_calculation;

        }else{
            return [];
        }
    }

}
