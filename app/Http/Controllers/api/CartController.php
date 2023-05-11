<?php

namespace App\Http\Controllers\api;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\Color;
use App\Model\Product;
use App\Model\ProductStock;
use App\Model\ShippingMethodRates;
use App\Model\CheckoutInfo;
use App\Model\Shop;
use App\Model\Order;
use App\Model\OrderDetail;
use App\User;
use Illuminate\Support\Str;
use App\Model\ShippingType;
use App\Model\CategoryShippingCost;
use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Tax;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class CartController extends Controller
{

    public function cart(Request $request)
    {
        $user = Helpers::get_customer($request);
        $cart = Cart::with('product:id,name,slug,current_stock,minimum_order_qty,variation')
            ->where(['customer_id' => $user->id])
            ->get();

        if($cart) {
            foreach($cart as $key => $value){
                if(!isset($value['product'])){
                    $cart_data = Cart::find($value['id']);
                    $cart_data->delete();

                    unset($cart[$key]);
                }
            }

            $cart->map(function ($data) {
                $data['choices'] = json_decode($data['choices']);
                $data['variations'] = json_decode($data['variations']);

                $data['product']['total_current_stock'] = isset($data['product']['current_stock']) ? $data['product']['current_stock'] : 0;
                if (isset($data['product']['variation']) && !empty($data['product']['variation'])) {
                    $variants = json_decode($data['product']['variation']);
                    foreach ($variants as $var) {
                        if ($data['variant'] == $var->type) {
                            $data['product']['total_current_stock'] = $var->qty;
                        }
                    }
                }
                unset($data['product']['variation']);

                return $data;
            });
        }

        return response()->json($cart, 200);
    }

    public function get_cart(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $cart = Cart::select('id','quantity','product_id','quantity','name','thumbnail')->where(['customer_id' => $user_details->id])->get();
        $total_cart_price = 0;
        if($cart) {
            foreach($cart as $key => $value){
                if(!isset($value['product'])){
                    $cart_data = Cart::find($value['id']);
                    $cart_data->delete();
                }
                $price = $value['product']['purchase_price'];
                unset($value['product']);
                $cart[$key]['total_current_stock'] = ProductStock::where('product_id',$value['product_id'])->where('is_purchased',0)->count() ?? 0;
                //$cart[$key]['price'] = $price;
                $cart[$key]['purchase_price'] = number_format($price,2);
                $total_cart_price += ($value['quantity'] * $price);
            }
        }

        return response()->json(['status'=>200,'message'=>'Success','total_price'=>number_format($total_cart_price,2),'data'=>$cart],200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'product_id' => 'required',
        ],[
            'product_id.required' => translate('Product ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $product = Product::find($request->product_id);
        $cart = Cart::where(['product_id' => $request->product_id, 'customer_id' => $user_details->id])->first();
        $current_stock = ProductStock::where('product_id',$request->product_id)->where('is_purchased',0)->count();
        if(isset($cart) == false){
            $cart = new Cart();
            if ($current_stock < 1) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('out_of_stock!')
                ], 200);
            }
        }else{
            if ($current_stock < $cart['quantity']) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('out_of_stock!')
                ], 200);
            }
        }

        if(isset($cart['quantity'])){
            $total_quantity = ($cart['quantity'] + 1);
        }else{
            $total_quantity = 1;
        }
      
        $price = $product['purchase_price'];
        $tax = Helpers::tax_calculation($price, $product['tax'], 'percent');
        $cart['customer_id'] = $user_details->id ?? 0;
        $cart['product_id'] = $request->product_id ?? 0;
        $cart['quantity'] = $total_quantity;
        $cart['price'] = $price;
        $cart['tax'] = $tax;
        $cart['name'] = $product->name;
        $cart['discount'] = Helpers::get_product_discount($product, $price);
        $cart['thumbnail'] = asset("/product/thumbnail/$product->thumbnail");
        $cart->save();
        //$cart = CartManager::add_to_cart($request);
        return response()->json([
            'status' => 1,
            'message' => translate('successfully_added!')
        ], 200);
    }

    // public function update_cart(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'id' => 'required',
    //         'quantity' => 'required',
    //     ], [
    //         'id.required' => translate('Cart ID is required!')
    //     ]);

    //     if ($validator->errors()->count() > 0) {
    //         return response()->json(['errors' => Helpers::error_processor($validator)]);
    //     }

    //     $status = 1;
    //     $qty = 0;
    //     $auth_token   = $request->headers->get('X-Access-Token');
    //     $user_details = User::where(['auth_access_token'=>$auth_token])->first();
    //     $cart = Cart::where(['id' => $request->id, 'customer_id' => $user_details->id])->first();
    //     //$product = Product::find($cart['product_id']);
    //     if(!empty($cart->id)){
    //         $current_stock = ProductStock::where('product_id',$cart['product_id'])->count();
    //         if ($current_stock < $request['quantity']) {
    //             $status = 0;
    //             $qty = $cart['quantity'];
    //         }

    //         if ($status) {
    //             $qty = $request->quantity;
    //             $cart['quantity'] = $request->quantity;
    //         }

    //         $cart->save();
    //         return response()->json([
    //             'status' => $status,
    //             'qty' => $qty,
    //             'message' => $status == 1 ? translate('successfully_updated!') : translate('sorry_stock_is_limited')
    //         ],200);

    //     }else{
    //         return response()->json(['status'=>400,'message'=>'No product added in cart please add first'],400);
    //     }

    // }

    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ], [
            'id.required' => translate('Cart ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $cart = Cart::find($request->id);
        if(isset($cart['quantity']) && $cart['quantity'] > 0){
            $cart->quantity  = ($cart['quantity'] - 1);
            $cart->save();
            return response()->json(['status'=>1,'message'=>translate('successfully_removed')],200);
        }else{
            return response()->json(['status'=>0,'message'=>'Item should not be empty'],200);
        }
       
    }

    public function remove_all_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ], [
            'id.required' => translate('Cart ID is required!')
        ]);
        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        Cart::find($request->id)->delete();
        return response()->json(['status'=>200,'message'=>translate('successfully_removed')],200);
    }

    public function checkout(Request $request)
    {
        $device_ids = [];
        $total_order = 0;
        $total_price = 0;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){

            $cart_info = Cart::select('id','customer_id','product_id','quantity','name','thumbnail')->where('customer_id',$user_details->id)->where('quantity','>',0)->get();
            if(!empty($cart_info)){
                foreach($cart_info as $k => $cart){
                    $total_order += $cart['quantity'];
                    $price = Product::select('purchase_price as price')->where('id',$cart['product_id'])->first()->price ?? 0;
                    $total_price += ($price * $cart['quantity']);
                    $cart['purchase_price'] = number_format($price,2);
                    array_push($device_ids,$cart['product_id']);
                }
            }

            CheckoutInfo::insert(['product_id'=>json_encode($device_ids),'customer_id'=>$user_details->id,'total_order'=>$total_order,'total_amount'=>$total_price,'tax_amount'=>7]);

            if (!empty($user_details->shipping_country) && !empty($user_details->shipping_state)) {
                $country_name = $this->getCountryName($user_details->shipping_country);
                $state_name = $this->getStateName($user_details->shipping_state);
            } else {
                $country_name = $this->getCountryName($user_details->country);
                $state_name = $this->getStateName($user_details->state);
            }
            
            $shipping_rates = ShippingMethodRates::select('normal_rate','express_rate','shipping_methods.title as shipping_company','shipping_methods.normal_duration','shipping_methods.express_duration','shipping_methods.id as shippingid')
                            ->join('shipping_methods','shipping_methods.id','shipping_method_rates.shipping_id')
                            ->where('shipping_method_rates.status',1)->where('country_code',$country_name)->get();

            //$shipping = number_format(0,2);
            $shipping_cost_check = [];
            if(!empty($shipping_rates)){
                foreach($shipping_rates as $k => $val){
                    $shipping_cost_check[$k]['id'] = $val['shippingid'];
                    $shipping_cost_check[$k]['company'] = $val['shipping_company'];
                    if($val['normal_rate'] < $val['express_rate']){
                        $shipping_cost_check[$k]['shipping_rate'] = $val['normal_rate'];
                        $shipping_cost_check[$k]['mode'] = "normal_rate";
                        $shipping_cost_check[$k]['delivery_days'] = $val['normal_duration'];
                    }else{
                        $shipping_cost_check[$k]['shipping_rate'] = $val['express_rate'];
                        $shipping_cost_check[$k]['mode'] = "express_rate";
                        $shipping_cost_check[$k]['delivery_days'] = $val['express_duration'];
                    }
                }
            }

            //TAX calculation
            //$tax_arr = $this->getTaxCalculation(20,"Canada","Quebec");
            $tax_arr = $this->getTaxCalculation($total_price,$country_name,$state_name);
            //END Tax calculation

            //echo "<pre>"; print_r($tax_arr); die;

            //$tax = "";
            // $taxes = [];
            // $tax_amt = $tax_arr['tax_amt'] ?? "0";
            // if(!empty($tax_arr['tax_percent']) && !empty($tax_arr['tax_name'])){
            //     $tax = "Total Tax " . $tax_arr['tax_percent'] . "% " . $tax_arr['tax_name'];
            // }
            
            $data['cart_info'] = $cart_info;
            $data['shipping_rates'] = $shipping_cost_check;
            $data['customer_id'] = $user_details->id;
            $data['total_order'] = $total_order;
            $data['sub_total'] = number_format($total_price, 2);
            //$data['shipping'] = $shipping;
            //$data['total_tax_percent'] = number_format($tax_arr['tax_percent'],3) ?? "0";
            //$data['total_tax_amount'] = number_format($tax_amt,2);
            //$data['tax_desc'] = $tax;
            $data['taxes'] = $tax_arr;
            //$data['total'] = number_format(($total_price), 2);
            //echo "<pre>"; print_r($mac_ids); die;

            return response()->json(['status'=>200,'message'=>'Success','data'=>$data],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }

        // here i will add stripe tax api and calculate price based on no of device and will send in response
    }

    public function place_order(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $cart_id = $request->cart_id;

        $shipping_id = $request->shipping_id;
        
        $left = ltrim($cart_id, "'");
        $right = rtrim($left, "'");
        $data = json_decode($right,true);
        $cart_ids = [];
        if(!empty($data)){
            foreach($data as $k => $val){
                array_push($cart_ids,$val['id']);
            }
        }

        $existed_mac_ids = [];
        $mac_ids_array = [];
        $total_price = 0;
        $error = 0;
        $check_mac_ids = Order::select('mac_ids')->get();
        if(!empty($check_mac_ids)){
            foreach($check_mac_ids as $mac_ids){
                $mac_id_arr = json_decode($mac_ids['mac_ids'],true);
                if(!empty($mac_id_arr)){
                    foreach($mac_id_arr as $product_id => $mac_values){
                        foreach($mac_values as $k => $mac_ids){
                            array_push($existed_mac_ids,$mac_ids);
                        }
                    }
                }
            }
        }

        if(!empty($user_details->id)){

            $cart_info = Cart::select('id','customer_id','product_id','price','quantity')->whereIn('id',$cart_ids)->get();
            //echo "<pre>"; print_r($cart_info); die;
            if(!empty($cart_info)){
                foreach($cart_info as $cart){
                    $price = Product::select('purchase_price as price')->where('id',$cart['product_id'])->first()->price ?? 0;
                    $total_price += ($price * $cart['quantity']);
                    $get_random_stocks = ProductStock::select('mac_id','product_id')->where('is_purchased',0)->where('product_id',$cart['product_id'])->whereNotIn('mac_id',$existed_mac_ids)
                                                        ->inRandomOrder()->limit($cart['quantity'])->get();
                    if(!empty($get_random_stocks)){
                        foreach($get_random_stocks as $m => $macid){
                            $mac_ids_array[$cart['product_id']][$m] = $macid['mac_id'];
                        }
                    }

                    if(!in_array($cart['product_id'],array_keys($mac_ids_array))){
                        $error = 1;
                    }
                }

                if($error == 1){
                    return response()->json(['status'=>400,'message'=>'Device not available'],400);
                }
                
                //Insert into Order
                $order = new Order();
                $order->customer_id = $user_details->id;
                $order->payment_method = 'Stripe';
                $order->shipping_method_id = $shipping_id;
                $order->mac_ids = json_encode($mac_ids_array);
                $order->order_amount = number_format($total_price,2);
                $order->save();

                if(!empty($order->mac_ids)){
                    $mac_ids = json_decode($order->mac_ids,true);
                    foreach($mac_ids as $product_id => $macs){
                        foreach($macs as $val){
                            ProductStock::where('product_id',$product_id)->where('mac_id',$val)->update(['is_purchased'=>1]);
                            Cart::where('customer_id',$user_details->id)->where('product_id',$product_id)->delete();
                        }
                    }
                }

                return response()->json(['status'=>200,'message'=>'Success','order_id'=>$order->id ?? NULL],200);

            }else{

                return response()->json(['status'=>400,'message'=>'No device found in cart'],200);
                
            }

        }else{

            return response()->json(['status'=>400,'message'=>'User not found'],200);

        }
    }

    public function confirm_order(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $order_id = $request->order_id;
        $transaction_id = $request->transaction_id;
        $update_order = Order::where(['id'=>$order_id])->first();
        if($update_order){
            
            $update_order->transaction_ref = $transaction_id;
            $update_order->payment_status = 'paid';
            $update_order->order_status = 'confirmed';
            $update_order->save();

            $msg = "Your Order has been placed, Estimated Delivery on " . date('F j',strtotime($update_order->created_at . '+7 days'));
            $payload['order_id'] = $update_order->id ?? NULL;
            $this->sendNotification($user_details->fcm_token,$msg,$payload);

            return response()->json(['status'=>200,'message'=>'Order Successfully Confirmed','order_id'=>(int)$order_id],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Order not Confirmed,something went wrong'],200);
        }
    }

    
    public function changeOrderStatus(Request $request){
        $order = Order::find($request->order_id);
        $order->order_status = $request->status;
        $order->save();
        $data = $request->order_status;

        $user = User::select('fcm_token')->where('id',$order->customer_id)->first();
        $msg = "Your Order is ". $request->order_status;
        $payload['order_id'] = $request->order_id;
        $this->sendNotification1($user->fcm_token ?? "",$msg,$payload);
        return response()->json(['status'=>200,'message'=>'Success']);
    }

    public function sendNotification($fcm_token,$msg,$payload){
        $SERVER_ID = env('FIREBASE_NOTIF_SERVER_ID');
		$FCM_URL   = env('FCM_URL');

		$registrationIds[] = $fcm_token; //$registration_id;
		$title             = 'Keepr';
		// prep the bundle
		$notification = [
			'title' => $title,
			'body' => $msg,
			'vibrate' => '1',
			'sound' => 'default',
		];

		$data1 = [
            'title' => $title,
            'message' => $msg,
            'vibrate' => 1,
            'sound' => 1,
            'type' => 'order_placed',
            'order_id'=> $payload['order_id']
		];

		$fields = array(
			'data' => $data1,
			'notification' => $notification,
			'registration_ids' => $registrationIds,
		);

		$headers = array(
			'Authorization: key=' . $SERVER_ID,
			'Content-Type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $FCM_URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		curl_close($ch);
        $res = json_decode($result,true);
        if(isset($res['success']) && $res['success'] == 1){
            return true;
        }else{
            return false;
        }
        //echo "<pre>"; print_r($result); die;
    }

    public function sendNotification1($fcm_token,$msg,$payload){
        $SERVER_ID = env('FIREBASE_NOTIF_SERVER_ID');
		$FCM_URL   = env('FCM_URL');

		$registrationIds[] = $fcm_token; //$registration_id;
		$title             = 'Keepr';
		// prep the bundle
		$notification = [
			'title' => $title,
			'body' => $msg,
			'vibrate' => '1',
			'sound' => 'default',
		];

		$data1 = [
            'title' => $title,
            'message' => $msg,
            'vibrate' => 1,
            'sound' => 1,
            'type' => 'order_status',
            'order_id'=>$payload['order_id']
		];

		$fields = array(
			'data' => $data1,
			'notification' => $notification,
			'registration_ids' => $registrationIds,
		);

		$headers = array(
			'Authorization: key=' . $SERVER_ID,
			'Content-Type: application/json',
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $FCM_URL);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
		$result = curl_exec($ch);
		curl_close($ch);
        $res = json_decode($result,true);
        if(isset($res['success']) && $res['success'] == 1){
            return true;
        }else{
            return false;
        }
        //echo "<pre>"; print_r($result); die;
    }
    
}
