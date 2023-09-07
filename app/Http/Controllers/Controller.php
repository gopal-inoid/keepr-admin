<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Mail;
use App\Model\EmailTemplates;
use App\Model\BusinessSetting;
use App\Model\ProductStock;
use App\Model\Order;
use App\Model\Product;
use App\Model\Admin;
use App\Model\ShippingMethod;
use App\Model\ShippingMethodRates;
use Illuminate\Support\Facades\View;
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

    public function CheckDeviceExists($type,$value){
        $check = ProductStock::where($type,$value)->count();
        if($check > 0){
            return true;
        }else{
            return false;
        }
    }

    public function print_r($str){
        echo "<pre>"; print_r($str); die;
    }

    public function getAdminDetail($field = null){
        if($field != null){
            return Admin::select($field)->first()->$field ?? "";
        }elseif($field != null && is_array($field)){
            return Admin::select(implode(',',$field))->first();
        }else{
            return Admin::first();
        }
    }

    public function getOrderAttr($mac_ids){
        $attr = [];
        $total_orders = 0;
        if(!empty($mac_ids)){
            $macids = json_decode($mac_ids,true);
            if(!empty($macids)){
                foreach($macids as $k => $val){
                    $total_orders += count($val['uuid']);
                    $product_name = $this->getProductAttr($k,'name');
                    $attr['product_name'][] = $product_name ?? "";
                    if(!empty($val)){
                        foreach($val['uuid'] as $k1 => $val1){ 
                            $attr['uuid'][] = $val1;
                        }
                    }
                }

                $attr['total_orders'] = $total_orders;
                return $attr;
            }
        }

        return [];
    }

    public function getProductAttr($product_id,$type){
        $products_attr = Product::select($type)->where('id',$product_id)->first();
        return $products_attr->$type ?? "";
    }

    public function getCountryName($id){
        $country_names = \DB::table('country')->select('name')->where('id',$id)->first();
        return $country_names->name ?? "";
    }

    public function getStateName($id){
        $state_names = \DB::table('states')->select('name')->where('id',$id)->first();
        return $state_names->name ?? "";
    }

    public function save_invoice($id)
    {
        $company_phone =BusinessSetting::where('type', 'company_phone')->first()->value;
        $company_email =BusinessSetting::where('type', 'company_email')->first()->value;
        $company_name =BusinessSetting::where('type', 'company_name')->first()->value;
        $company_web_logo =BusinessSetting::where('type', 'company_web_logo')->first()->value;
        $order = Order::where('id', $id)->first();
        $data["email"] = $order->customer !=null?$order->customer["email"]:\App\CPU\translate('email_not_found');
        $data["client_name"] = $order->customer !=null? $order->customer["f_name"] . ' ' . $order->customer["l_name"]:\App\CPU\translate('customer_not_found');
        $data["order"] = $order;
        $products = [];
        $tax_info = [];
        $shipping_info = [];
        $total_orders = 0;
        $total_order_amount = $order->order_amount ?? 0;
        if(!empty($order->mac_ids)){ // stocks
            $mac_ids = json_decode($order->mac_ids,true);
            if(!empty($mac_ids)){

                if(!empty($order->taxes)){
                    $taxes = json_decode($order->taxes,true);
                    if(!empty($taxes)){
                        $tax_info = $taxes;
                    }
                }

                if(!empty($order->shipping_method_id) && !empty($order->shipping_mode)){
                    $shipping = ShippingMethod::where(['id' => $order->shipping_method_id])->first();
                    $shipping_method_rates = ShippingMethodRates::select('normal_rate','express_rate')->where('shipping_id',$order->shipping_method_id)->where('country_code',$this->getCountryName($order->customer->country))->first();
                    $shipping_info['title'] = $shipping->title ?? "";
                    if($order->shipping_mode == 'normal_rate'){
                        $shipping_info['duration'] = $shipping->normal_duration ?? "";
                        $shipping_info['mode'] = 'Regular Rate';
                        $shipping_info['amount'] = $shipping_method_rates->normal_rate ?? 0;
                    }elseif($order->shipping_mode == 'express_rate'){
                        $shipping_info['duration'] = $shipping->express_duration ?? "";
                        $shipping_info['mode'] = 'Express Rate';
                        $shipping_info['amount'] = $shipping_method_rates->express_rate ?? 0;
                    }
                }

                foreach($mac_ids as $k => $val){
                    $total_orders += count($mac_ids[$k]['uuid']);
                    $prod = Product::select('name','thumbnail','purchase_price')->find($k);
                    $products[$k]['name'] = $prod->name ?? "";
                    $products[$k]['thumbnail'] = $prod->thumbnail ?? "";
                    if(!empty($order->per_device_amount)){
                        $perdevice_amount = json_decode($order->per_device_amount,true);
                        if(!empty($perdevice_amount)){
                            $products[$k]['price'] = $perdevice_amount[$k] ?? 0;
                        }else{
                            $products[$k]['price'] = $prod->purchase_price ?? 0;
                        }
                    }else{
                        $products[$k]['price'] = $prod->purchase_price ?? 0;
                    }
                    if(!empty($val)){
                        foreach($val['uuid'] as $k1 => $val1){ 
                            $products[$k]['mac_ids'][$k1]['uuid'] = $val1;
                            $products[$k]['mac_ids'][$k1]['major'] = $val['major'][$k1];
                            $products[$k]['mac_ids'][$k1]['minor'] = $val['minor'][$k1];
                        }
                    }
                }
            }
        }
        $mpdf_view = View::make('admin-views.order.invoice',
            compact('order', 'company_phone','total_orders','products', 'company_name', 'company_email', 'company_web_logo','total_order_amount','shipping_info','tax_info')
        );
        Helpers::save_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function sendKeeprEmail($template_type,$user_data,$attachment = null){
        $emailServices_smtp = Helpers::get_business_settings('mail_config');
        $files = null;
        if($attachment != null){
            $files = [$attachment];
        }
        if ($emailServices_smtp['status'] == 1) {
            try{
                $email_temp = EmailTemplates::where(['name' => $template_type])->where('status', 1)->first();
                if(!empty($email_temp->id)){
                    $email_temp->subject = str_replace("{STATUS}", $user_data['order_status'] ?? "", $email_temp->subject);
                    $email_temp->body = str_replace("{USERNAME}", $user_data['username'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_ID}", $user_data['order_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{PRODUCT_NAME}", $user_data['product_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{DEVICE_UUID}", $user_data['device_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{QTY}", $user_data['qty'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TOTAL_PRICE}", $user_data['total_price'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_DATE}", $user_data['order_date'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_NOTE}", $user_data['order_note'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_NAME}", $user_data['billing_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_EMAIL}", $user_data['billing_email'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_ADDRESS}", $user_data['billing_address'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_NAME}", $user_data['shipping_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_EMAIL}", $user_data['shipping_email'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_ADDRESS}", $user_data['shipping_address'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPMENT_INFORMATION}", $user_data['shipment_information'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ESTIMATED_DELIVERY_DATE}", $user_data['estimated_delivery_date'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TRACKING_ID}", $user_data['tracking_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{COMPANY_NAME}", 'Keepr', $email_temp->body);
                    $email_temp->body = str_replace("{COMPANY_LOGO}", '<img src="'.url('/public/public/company/Keepe_logo.png').'" />', $email_temp->body);
                    $data['email'] = $user_data['email'] ?? "";
                    $data['subject'] = $email_temp->subject ?? "";
                    $data["body"] = $email_temp->body ?? "";

                    if(!empty($data['email'])){
                        Mail::send('email-templates.mail-tester', $data, function($message)use($data, $files) {
                            $message->to($data["email"])
                                    ->subject($data["subject"]);
                            if($files != null){
                                foreach ($files as $file){
                                    $message->attach($file);
                                }
                            }
                        });
                    }
                    
                }
            }catch(\Exception $e){
                $error = $e->getMessage();
            }
            if(isset($error)){
                return false;
            }else{
                return true;
            }
        }
        
        return true;
    }

    public function replacedEmailVariables($status,$body,$userData = null){
        if($userData != null){
            $notif_keys = ["{STATUS}","{USERNAME}","{ORDER_ID}","{PRODUCT_NAME}",
                           "{DEVICE_UUID}","{QTY}","{TOTAL_PRICE}","{COMPANY_NAME}",
                           "{COMPANY_LOGO}","{ORDER_DATE}","{ORDER_NOTE}","{BILLING_NAME}",
                           "{BILLING_EMAIL}","{BILLING_ADDRESS}","{SHIPPING_NAME}","{SHIPPING_EMAIL}",
                           "{SHIPPING_ADDRESS}","{SHIPMENT_INFORMATION}","{ESTIMATED_DELIVERY_DATE}","{TRACKING_ID}"];

            $notif_values   = [$status,$userData['username'],$userData['order_id'],$userData['product_name'],
                               $userData['device_id'],$userData['qty'],$userData['total_price'],$userData['company_name'],
                               $userData['company_logo']];
        }else{
            $notif_keys = ["{STATUS}"];
            $notif_values   = [$status];
        }
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
