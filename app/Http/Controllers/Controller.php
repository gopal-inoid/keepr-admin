<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
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

    public function getTaxCalculation($amount,$country_name,$state_name){
        $taxes = \DB::table('tax_calculation')->select('tax_amt','type')->where('country',$country_name)->first();
        $tax_calculation = [];
        if(!empty($taxes)){
            $tax_rates = json_decode($taxes->tax_amt,true);
            if($taxes->type == "fixed"){
                if($tax_rates[0]['tax1'] != ""){
                    $tax_amt = (($amount * $tax_rates[0]['tax1']) / 100);
                    $tax_calculation['tax_amt'] = $tax_amt;
                    $tax_calculation['tax_percent'] = $tax_rates[0]['tax1'];
                    $tax_calculation['tax_name'] = $tax_rates[0]['tax_txt1'];
                }else{
                    return [];
                }
            }else{
                //echo "<pre>"; print_r($tax_rates); die;
                foreach($tax_rates as $taxval){
                    if($state_name == $taxval['state']){
                        $tax_amt1 = (($amount * $taxval['tax1']) / 100);
                        if(!empty($taxval['tax2'])){
                            $tax_amt2 = (($amount * $taxval['tax2']) / 100);
                        }
                        $tax_calculation['tax_amt'] = $tax_amt1 + ($tax_amt2 ?? 0);
                        $tax_calculation['tax_percent'] = $taxval['tax1'] + ($taxval['tax2'] ?? 0);
                        $tax_calculation['tax_name'] = $taxval['tax1'] . "% " . $taxval['tax_txt1'] . (!empty($taxval['tax_txt2']) ? ",". $taxval['tax2'] . "% " . $taxval['tax_txt2']:"");
                    }
                }
            }

            return $tax_calculation;

        }else{
            return [];
        }
    }

}
