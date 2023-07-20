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
use App\Common;
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
        $cart = Cart::select('id','quantity','product_id','quantity','name','thumbnail','color')->where(['customer_id' => $user_details->id])->where('quantity', '>', 0)->get();
        $total_cart_price = 0;
        if($cart) {
            foreach($cart as $key => $value){
                $colorStocks = Color::select('name')->where('id',$value->color)->first();
                $value['color'] = $colorStocks->name ?? NULL;
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
        Common::addLog([]);
        return response()->json(['status'=>200,'message'=>'Success','total_price'=>number_format($total_cart_price,2),'data'=>$cart],200);
    }

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'product_id' => 'required',
            'color' => 'required'
        ],[
            'product_id.required' => translate('Product ID is required!'),
            'color.required' => translate('Color is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $colorStocks = Color::select('id')->where('name',$request->color)->first();
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $product = Product::find($request->product_id);
        $cart = Cart::where(['product_id' => $request->product_id,'color'=>$colorStocks->id ?? NULL, 'customer_id' => $user_details->id])->first();
        $current_stock = ProductStock::where('product_id',$request->product_id)->where('color',$colorStocks->id ?? NULL)->where('is_purchased',0)->count();
        if(isset($cart) == false){
            $cart = new Cart();
            if ($current_stock < 1) {
                return response()->json([
                    'status' => 0,
                    'message' => translate('out_of_stock!')
                ], 200);
            }
        }else{
            if ($current_stock <= $cart['quantity']) {
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
        $cart['color'] = $colorStocks->id ?? NULL;
        $cart['quantity'] = $total_quantity;
        $cart['price'] = $price;
        $cart['tax'] = $tax;
        $cart['name'] = $product->name;
        $cart['discount'] = Helpers::get_product_discount($product, $price);
        $cart['thumbnail'] = asset("/product/thumbnail/$product->thumbnail");
        $cart->save();
        //$cart = CartManager::add_to_cart($request);
        Common::addLog([]);
        return response()->json([
            'status' => 1,
            'message' => translate('successfully_added!')
        ], 200);

    }

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
            Common::addLog([]);
            return response()->json(['status'=>1,'message'=>translate('successfully_removed')],200);
        }else{
            Common::addLog([]);
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
        Common::addLog([]);
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
                            ->where('shipping_methods.status',1)->where('shipping_method_rates.status',1)->where('country_code',$country_name)->get();

            //$shipping = number_format(0,2);
            $shipping_cost_check = [];
            if(!empty($shipping_rates)){
                foreach($shipping_rates as $k => $val){
                    $shipping_cost_check[$k]['id'] = $val['shippingid'];
                    $shipping_cost_check[$k]['company'] = $val['shipping_company'];
                    if($val['normal_rate'] < $val['express_rate']){
                        $shipping_cost_check[$k]['shipping_rate'] = $val['normal_rate'];
                        $shipping_cost_check[$k]['mode'] = "normal_rate";
                        $shipping_cost_check[$k]['text'] = "Regular Rate";
                        $shipping_cost_check[$k]['delivery_days'] = $val['normal_duration'];
                    }else{
                        $shipping_cost_check[$k]['shipping_rate'] = $val['express_rate'];
                        $shipping_cost_check[$k]['mode'] = "express_rate";
                        $shipping_cost_check[$k]['text'] = "Express Rate";
                        $shipping_cost_check[$k]['delivery_days'] = $val['express_duration'];
                    }
                }
            }
            //TAX calculation
            $tax_arr = $this->getTaxCalculation($total_price,$country_name,$state_name);
            //END Tax calculation
            $data['cart_info'] = $cart_info;
            $data['shipping_rates'] = $shipping_cost_check;
            $data['customer_id'] = $user_details->id;
            $data['total_order'] = $total_order;
            $data['sub_total'] = number_format($total_price, 2);
            $data['taxes'] = $tax_arr;
            Common::addLog([]);
            return response()->json(['status'=>200,'message'=>'Success','data'=>$data],200);
        }else{
            Common::addLog([]);
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function place_order(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $email_templates = $this->getEmailTemplate('order-placed');
        $cart_id = $request->cart_id;
        $shipping_id = $request->shipping_id ?? "";
		$taxes = $request->tax;
		$shipping_rate_id = $request->shipping_rate_id;
		$shipping_mode = $request->shipping_mode;
        $left = ltrim($cart_id, "'");
        $right = rtrim($left, "'");
        $data = json_decode($right,true);
        $cart_ids = [];
        if(!empty($data)){
            foreach($data as $k => $val){
                array_push($cart_ids,$val['id']);
            }
        }
        $existed_mac_ids =  $mac_ids_array = [];
        $total_price = $error = 0;
        $check_mac_ids = Order::select('mac_ids')->get();
        if(!empty($check_mac_ids)){
            foreach($check_mac_ids as $mac_ids){
                $mac_id_arr = json_decode($mac_ids['mac_ids'],true);
                if(!empty($mac_id_arr)){
                    foreach($mac_id_arr as $product_id => $mac_values){
                        foreach($mac_values as $k => $mac_ids){
                            //array_push($existed_mac_ids,$mac_ids['uuid']);
                            $existed_mac_ids[$k][] = $mac_ids;
                        }
                    }
                }
            }
        }
        if(!empty($user_details->id)){

            $cart_info = Cart::select('id','customer_id','product_id','price','quantity')->where('quantity', '>',0)->whereIn('id',$cart_ids)->get();
            //echo "<pre>"; print_r($cart_info); die;
            if(!empty($cart_info[0])){
                foreach($cart_info as $cart){
                    $price = Product::select('purchase_price as price')->where('id',$cart['product_id'])->first()->price ?? 0;
                    $total_price += ($price * $cart['quantity']);
                    $get_random_stocks = ProductStock::select('uuid','major','minor','product_id')->where('is_purchased',0)
                                                      ->where('product_id',$cart['product_id'])
                                                      ->inRandomOrder()->limit($cart['quantity'])->get();
                    if(!empty($get_random_stocks)){
                        foreach($get_random_stocks as $m => $macid){
                            if(!empty($existed_mac_ids) && (in_array($macid['uuid'],$existed_mac_ids['uuid']) && in_array($macid['major'],$existed_mac_ids['major']) && in_array($macid['minor'],$existed_mac_ids['minor']))){
                            }else{
                                $mac_ids_array[$cart['product_id']]['uuid'][] = $macid['uuid'];
                                $mac_ids_array[$cart['product_id']]['major'][] = $macid['major'];
                                $mac_ids_array[$cart['product_id']]['minor'][] = $macid['minor'];
                            }
                        }
                    }
                    if(!in_array($cart['product_id'],array_keys($mac_ids_array))){
                        $error = 1;
                    }
                }

                if($error == 1){
                    return response()->json(['status'=>400,'message'=>'Device not available'],400);
                }

                $stripe_payment_create = $this->CreateCheckout($total_price);
                if(!empty($stripe_payment_create['id'])){
                    $intent_data['id'] = $stripe_payment_create['id'];
                    $intent_data['client_secret'] = $stripe_payment_create['client_secret'];
                    $intent_data['amount'] = $stripe_payment_create['amount'];
                }

                $order = new Order();
                $order->customer_id = $user_details->id;
                $order->payment_method = 'Stripe';
                $order->shipping_method_id = $shipping_id;
                $order->taxes = $taxes;
                $order->shipping_rate_id = $shipping_rate_id;
				$order->shipping_mode = $shipping_mode;
                $order->mac_ids = json_encode($mac_ids_array);
                $order->order_amount = number_format($total_price,2);
                $order->save();
                $this->sendEmail($user_details->email, $email_templates->subject ?? "Order Placed", $email_templates->body ?? "Order has been Placed");
                if(!empty($order->mac_ids)){
                    $mac_ids = json_decode($order->mac_ids,true);
                    foreach($mac_ids as $product_id => $mac_values){
                        foreach($mac_values as $k => $macss){
                            foreach($macss as $m => $macs){
                                ProductStock::where('product_id',$product_id)->where(['uuid'=>$mac_values['uuid'][$m],'major'=>$mac_values['major'][$m],'minor'=>$mac_values['minor'][$m]])->update(['is_purchased'=>1]);
                            }
                        }
                        Cart::where('customer_id',$user_details->id)->where('product_id',$product_id)->delete();
                    }
                }

                Common::addLog([]);
                return response()->json(['status'=>200,'message'=>'Success','stripe_intent'=>$intent_data,'order_id'=>$order->id ?? NULL],200);

            }else{
                Common::addLog([]);
                return response()->json(['status'=>400,'message'=>'No device found in cart'],200);
                
            }

        }else{
            Common::addLog([]);
            return response()->json(['status'=>400,'message'=>'User not found'],200);

        }
    }

    public function confirmed_payment_intent(Request $request){

        $trans_id = $request->trans_id;
        $stripe = new \Stripe\StripeClient('sk_test_51MprMPC6n3N1q7nDsYGlAYsLmkhVVQ2LAQqbInlthpU9FoUdqsNy9jT8uhMRrg1e6KtptrHJhY5iwJc3ASXxALeg005ync97Mg');
        $data = $stripe->paymentIntents->retrieve($trans_id);
        echo "<pre>"; print_r($data); die;

    }

    public function verify_payment_intent($trans_id){
        $stripe = new \Stripe\StripeClient('sk_test_51MprMPC6n3N1q7nDsYGlAYsLmkhVVQ2LAQqbInlthpU9FoUdqsNy9jT8uhMRrg1e6KtptrHJhY5iwJc3ASXxALeg005ync97Mg');
        $data = $stripe->paymentIntents->retrieve($trans_id);
        if(!empty($data->status) && $data->status == 'succeeded'){
            return true;
        }else{
            return false;
        }
    }

    public function confirm_order(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $email_templates = $this->getEmailTemplate('order-confirmed');
        $order_id = $request->order_id;
        $transaction_id = $request->transaction_id;
        $is_verified = $this->verify_payment_intent($transaction_id);
        if(empty($is_verified)){
            return response()->json(['status'=>400,'message'=>'Payment failed'],200);
        }
        $update_order = Order::where(['id'=>$order_id])->first();
        if($update_order){
            $update_order->transaction_ref = $transaction_id;
            $update_order->payment_status = 'paid';
            $update_order->order_status = 'processing';
            $update_order->save();
            $this->sendEmail($user_details->email, $email_templates->subject ?? "Order Confirmed", $email_templates->body ?? "Order has been Confirmed");
            $payload['order_id'] = $update_order->id ?? NULL;
            $msg = "Your Order has been confirmed with Order ID #" . $payload['order_id'];
            $this->sendNotification($user_details->fcm_token,$msg,$payload);
            Common::addLog([]);
            return response()->json(['status'=>200,'message'=>'Order Successfully Confirmed','order_id'=>(int)$order_id],200);
        }else{
            Common::addLog([]);
            return response()->json(['status'=>400,'message'=>'Order not Confirmed,something went wrong'],200);
        }
    }

    public function CreateCheckout($amount){
        $stripe = new \Stripe\StripeClient('sk_test_51MprMPC6n3N1q7nDsYGlAYsLmkhVVQ2LAQqbInlthpU9FoUdqsNy9jT8uhMRrg1e6KtptrHJhY5iwJc3ASXxALeg005ync97Mg');
        $paymentIntents = $stripe->paymentIntents->create([
            'amount' => round($amount, 2) * 100,
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);
       return $paymentIntents;
    }

    public function getPaymentIntent(Request $request){
        $order_data = Order::select('order_amount')->where('id',$request->order_id)->first();
        if(!empty($order_data->order_amount)){
           $payment_intent = $this->CreateCheckout($order_data->order_amount);
           if(!empty($payment_intent['id'])){
                $intent_data['id'] = $payment_intent['id'];
                $intent_data['client_secret'] = $payment_intent['client_secret'];
                $intent_data['amount'] = $payment_intent['amount'];
                return response()->json(['status'=>200,'message'=>'Success','data'=>$intent_data],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Intent error got from stripe'],200);
            }
        }else{
           return response()->json(['status'=>400,'message'=>'Order not found'],200);
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
        Common::addLog([]);
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
