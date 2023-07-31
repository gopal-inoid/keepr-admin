<?php

namespace App\Http\Controllers\Admin;

use App\CPU\BackEndHelper;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\BusinessSetting;
use App\Model\DeliveryMan;
use App\Model\DeliveryManTransaction;
use App\Model\DeliverymanWallet;
use App\Model\Order;
use App\Model\Product;
use App\Model\ShippingMethod;
use App\Model\ShippingMethodRates;
use App\Model\Country;
use App\Model\State;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Model\Seller;
use App\User;
use App\Traits\CommonTrait;
use App\Model\ShippingAddress;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Ramsey\Uuid\Uuid;
use function App\CPU\translate;
use App\CPU\CustomerManager;
use App\CPU\Convert;
use Rap2hpoutre\FastExcel\FastExcel;

class OrderController extends Controller
{
    use CommonTrait;
    public function list(Request $request, $status)
    {
        $search = $request['search'];
        $filter = $request['filter'];
        $from = $request['from'];
        $to = $request['to'];
        $key = $request['search'] ? explode(' ', $request['search']) : '';
        $delivery_man_id = $request['delivery_man_id'];

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'seller.shop'])
            ->when($status != 'all', function ($q) use($status){
                $q->where(function ($query) use ($status) {
                    $query->orWhere('order_status', $status);
                });
            })
            ->when($filter,function($q) use($filter){
                $q->when($filter == 'all', function($q){
                    return $q;
                })
                    ->when($filter == 'POS', function ($q){
                        $q->whereHas('details', function ($q){
                            $q->where('order_type', 'POS');
                        });
                    })
                    ->when($filter == 'admin' || $filter == 'seller', function($q) use($filter){
                        $q->whereHas('details', function ($query) use ($filter){
                            $query->whereHas('product', function ($query) use ($filter){
                                $query->where('added_by', $filter);
                            });
                        });
                    });
            })
            ->when($request->has('search') && $search!=null,function ($q) use ($key) {
                $q->where(function($qq) use ($key){
                    foreach ($key as $value) {
                        $qq->where('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_ref', 'like', "%{$value}%");
                    }});
            })->when(!empty($from) && !empty($to), function($dateQuery) use($from, $to) {
                $dateQuery->whereDate('created_at', '>=',$from)
                    ->whereDate('created_at', '<=',$to);
            })->when($delivery_man_id, function ($q) use($delivery_man_id){
                $q->where(['delivery_man_id'=> $delivery_man_id, 'seller_is'=>'admin']);
            })
            ->latest()
            ->paginate(Helpers::pagination_limit())
            ->appends(['search'=>$request['search'],'filter'=>$request['filter'],'from'=>$request['from'],'to'=>$request['to'],'delivery_man_id'=>$request['delivery_man_id']]);
            return view('admin-views.order.list',compact('orders','search','from', 'to', 'status','filter'));
    }

    public function common_query_status_count($query, $status, $request){
        $search = $request['search'];
        $filter = $request['filter'];
        $from = $request['from'];
        $to = $request['to'];
        $key = $request['search'] ? explode(' ', $request['search']) : '';

            return $query->when($status != 'all', function ($q) use($status){
                $q->where(function ($query) use ($status) {
                    $query->orWhere('order_status', $status);
                });
            })
            ->when($filter,function($q) use($filter) {
                $q->when($filter == 'all', function ($q) {
                    return $q;
                })
                ->when($filter == 'POS', function ($q){
                    $q->whereHas('details', function ($q){
                        $q->where('order_type', 'POS');
                    });
                })
                ->when($filter == 'admin' || $filter == 'seller', function($q) use($filter){
                    $q->whereHas('details', function ($query) use ($filter){
                        $query->whereHas('product', function ($query) use ($filter){
                            $query->where('added_by', $filter);
                        });
                    });
                });
            })
            ->when($request->has('search') && $search!=null,function ($q) use ($key) {
                $q->where(function($qq) use ($key){
                    foreach ($key as $value) {
                        $qq->where('id', 'like', "%{$value}%")
                            ->orWhere('order_status', 'like', "%{$value}%")
                            ->orWhere('transaction_ref', 'like', "%{$value}%");
                    }});
            })->when(!empty($from) && !empty($to), function($dateQuery) use($from, $to) {
                $dateQuery->whereDate('created_at', '>=',$from)
                    ->whereDate('created_at', '<=',$to);
            })->count();
    }

    public function details($id)
    {
        $company_name =BusinessSetting::where('type', 'company_name')->first()->value;
        $company_web_logo =BusinessSetting::where('type', 'company_web_logo')->first()->value;
        $order = Order::where(['id' => $id])->first();
        $physical_product = false;
        $total_delivered = Order::where(['order_status' => 'delivered'])->count();
        $shipping_method = Helpers::get_business_settings('shipping_method');
        $countries = \DB::table('country')->select('name','id')->get();
        $states = \DB::table('states')->select('name','id')->get();

        $products = $tax_info = $shipping_info = [];
        $total_orders = 0;
        $total_order_amount = $order->order_amount ?? 0;
        if(!empty($order->mac_ids)){ // stocks
            $mac_ids = json_decode($order->mac_ids,true);
            if(!empty($mac_ids)){
                foreach($mac_ids as $k => $val){
                    $total_orders += count($mac_ids[$k]['uuid']);
                    $prod = Product::select('name','thumbnail','purchase_price')->find($k);
                    $products[$k]['name'] = $prod->name ?? "";
                    $products[$k]['thumbnail'] = $prod->thumbnail ?? "";
                    $products[$k]['price'] = $prod->purchase_price ?? 0;
                    if(!empty($val)){
                        foreach($val['uuid'] as $k1 => $val1){ 
                            $products[$k]['mac_ids'][$k1]['uuid'] = $val1;
                            $products[$k]['mac_ids'][$k1]['major'] = $val['major'][$k1];
                            $products[$k]['mac_ids'][$k1]['minor'] = $val['minor'][$k1];
                        }
                    }

                    if(!empty($order->taxes)){
                        $taxes = json_decode($order->taxes,true);
                        if(!empty($taxes)){
                            $tax_info[$k] = $taxes;
                        }
                    }

                    if(!empty($order->shipping_method_id) && !empty($order->shipping_mode)){
                        $shipping = ShippingMethod::where(['id' => $order->shipping_method_id])->first();
                        $shipping_method_rates = ShippingMethodRates::select('normal_rate','express_rate')->where('shipping_id',$order->shipping_method_id)->where('country_code',$this->getCountryName($order->customer->country))->first();
                        $shipping_info[$k]['title'] = $shipping->title ?? "";
                        if($order->shipping_mode == 'normal_rate'){
                            $shipping_info[$k]['duration'] = $shipping->normal_duration ?? "";
                            $shipping_info[$k]['mode'] = 'Regular Rate';
                            $shipping_info[$k]['amount'] = $shipping_method_rates->normal_rate ?? 0;
                        }elseif($order->shipping_mode == 'express_rate'){
                            $shipping_info[$k]['duration'] = $shipping->express_duration ?? "";
                            $shipping_info[$k]['mode'] = 'Express Rate';
                            $shipping_info[$k]['amount'] = $shipping_method_rates->express_rate ?? 0;
                        }

                        if(!empty($shipping_info[$k]['amount'])){
                            $total_order_amount += $shipping_info[$k]['amount'];
                        }
                    }
                }
            }
        }
       
        //echo "<pre>"; print_r($tax_info); die;
        return view('admin-views.pos.order.order-details', compact('order','total_orders','products', 'company_name', 'company_web_logo','countries','states','shipping_info','tax_info','total_order_amount'));
    }

    public function update_order_details(Request $request)
    {
        //echo "<pre>"; print_r($request->all()); die;

        $order_id = $request->order_id;
        $user_id = $request->user_id;
        $email_templates = $this->getEmailTemplate('order-status-change');
        if(!empty($order_id)){
            if(!empty($user_id)){

                $user_details = User::select('fcm_token','email')->where(['id'=>$user_id])->first();

                $user_data['name'] = $request->billing_name;
                $user_data['email'] = $request->email;
                $user_data['street_address'] = $request->street_address;
                $user_data['city'] = $request->billing_city;
                $user_data['state'] = $request->billing_state;
                $user_data['country'] = $request->billing_country;
                $user_data['zip'] = $request->billing_zip;
                $user_data['phone'] = $request->billing_phone;
                $user_data['shipping_name'] = $request->shipping_name;
                $user_data['shipping_email'] = $request->shipping_email;
                $user_data['add_shipping_address'] = $request->add_shipping_address;
                $user_data['shipping_city'] = $request->shipping_city;
                $user_data['shipping_state'] = $request->shipping_state;
                $user_data['shipping_country'] = $request->shipping_country;
                $user_data['shipping_zip'] = $request->shipping_zip;
                $user_data['shipping_phone'] = $request->shipping_phone;

                if(!empty($request->is_billing_address_same) && $request->is_billing_address_same == 'on'){
                    $user_data['is_billing_address_same'] =  1;
                }

                User::where('id',$user_id)->update($user_data);

                //SEND PUSH NOTIFICATION
                $msg = "Your Order has been " . $request->change_order_status . ", Order ID #" . $order_id;
                $payload['order_id'] = $order_id;
                $this->sendNotification($user_details->fcm_token,$msg,$payload);
                //

            }

            $order_data['order_status'] = $request->change_order_status;
            $order_data['created_at'] = date('Y-m-d h:i:s',strtotime($request->order_date));
            $order_data['order_note'] = $request->order_note;
            $order_data['expected_delivery_date'] = date('Y-m-d h:i:s',strtotime($request->expected_delivery_date));
            $order_data['shipment_info'] = $request->shipment_info;
            $order_data['transaction_ref'] = $request->transaction_ref;
            $order_data['payment_method'] = $request->payment_method;
            $order_data['payment_status'] = $request->payment_status;
            $order_data['tracking_id'] = $request->tracking_id;

            $get_order = Order::where('id',$order_id)->first();
            $order_attribute = $this->getOrderAttr($get_order->mac_ids);
            //$this->print_r($a);
            if(!empty($order_attribute['product_name']) && is_array($order_attribute['product_name'])){
                $product_names = implode(',',$order_attribute['product_name']);
            }
            if(!empty($order_attribute['uuid']) && is_array($order_attribute['uuid'])){
                $product_uuid = implode(',',$order_attribute['uuid']);
            }
            $userData['username'] = $user_data['name'] ?? "Keepr User";
            $userData['order_id'] = $order_id;
            $userData['product_name'] = $product_names;
            $userData['device_id'] = $product_uuid;
            $userData['qty'] = $order_attribute['total_orders'] ?? 0;
            $userData['total_price'] = $get_order->order_amount ?? "";
            $userData['company_name'] = 'Keepr';
            $userData['company_logo'] = '<img height="70px;" src="'.url('/public/public/company/Keepe_logo.png').'" />';
            //SEND ORDER EMAIL
            $subject = $this->replacedEmailVariables($request->change_order_status,$email_templates->subject ?? "Order");
            $body = $this->replacedEmailVariables($request->change_order_status,$email_templates->body ?? "Order status has been changed",$userData);
            //$this->save_invoice($request->id);
            //$invoice_file_path = public_path('public/assets/orders/order_invoice_'.$request->id.'.pdf');
            $this->sendEmail($user_details->email ?? "", $subject, $body);
            Order::where('id',$order_id)->update($order_data);
            return redirect()->back()->with('success','Order Details Updated Successfully');
        }else{
            return redirect()->back()->with('error','Order not found');
        }
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        $order->delivery_man_id = $delivery_man_id;
        $order->delivery_type = 'self_delivery';
        $order->delivery_service_name = null;
        $order->third_party_delivery_tracking_id = null;
        $order->save();

        $fcm_token = isset($order->delivery_man) ? $order->delivery_man->fcm_token : null;
        $value = Helpers::order_status_update_message('del_assign') . " ID: " . $order['id'];
        if(!empty($fcm_token)) {
            try {
                if ($value != null) {
                    $data = [
                        'title' => translate('order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                    ];

                    if ($order->delivery_man_id) {
                        self::add_deliveryman_push_notification($data, $order->delivery_man_id);
                    }
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
                Toastr::warning(\App\CPU\translate('Push notification failed for DeliveryMan!'));
            }
        }

        return response()->json(['status' => true], 200);
    }

    public function status(Request $request)
    {
        $user_id = auth('admin')->id();

        $order = Order::find($request->id);

        if(!isset($order->customer))
        {
            return response()->json(['customer_status'=>0],200);
        }

        $wallet_status = Helpers::get_business_settings('wallet_status');
        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');

        if($request->order_status=='delivered' && $order->payment_status !='paid'){

            return response()->json(['payment_status'=>0],200);
        }
        $fcm_token = isset($order->customer) ? $order->customer->cm_firebase_token : null;
        $value = Helpers::order_status_update_message($request->order_status);
        if(!empty($fcm_token)) {
            try {
                if ($value) {
                    $data = [
                        'title' => translate('Order'),
                        'description' => $value,
                        'order_id' => $order['id'],
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                }
            } catch (\Exception $e) {
            }
        }

        try {
            $fcm_token_delivery_man = $order->delivery_man->fcm_token;
            if ($request->order_status == 'canceled' && $value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                if($order->delivery_man_id) {
                    self::add_deliveryman_push_notification($data, $order->delivery_man_id);
                }
                Helpers::send_push_notif_to_device($fcm_token_delivery_man, $data);
            }
        } catch (\Exception $e) {
        }

        $order->order_status = $request->order_status;
        OrderManager::stock_update_on_order_status_change($order, $request->order_status);
        $order->save();

        if($loyalty_point_status == 1)
        {
            if($request->order_status == 'delivered' && $order->payment_status =='paid'){
                CustomerManager::create_loyalty_point_transaction($order->customer_id, $order->id, Convert::default($order->order_amount-$order->shipping_cost), 'order_place');
            }
        }

        if ($order->delivery_man_id && $request->order_status == 'delivered') {
            $dm_wallet = DeliverymanWallet::where('delivery_man_id', $order->delivery_man_id)->first();
            $cash_in_hand = $order->payment_method == 'cash_on_delivery' ? $order->order_amount : 0;

            if (empty($dm_wallet)) {
                DeliverymanWallet::create([
                    'delivery_man_id' => $order->delivery_man_id,
                    'current_balance' => BackEndHelper::currency_to_usd($order->deliveryman_charge) ?? 0,
                    'cash_in_hand' => BackEndHelper::currency_to_usd($cash_in_hand),
                    'pending_withdraw' => 0,
                    'total_withdraw' => 0,
                ]);
            } else {
                $dm_wallet->current_balance += BackEndHelper::currency_to_usd($order->deliveryman_charge) ?? 0;
                $dm_wallet->cash_in_hand += BackEndHelper::currency_to_usd($cash_in_hand);
                $dm_wallet->save();
            }

            if($order->deliveryman_charge && $request->order_status == 'delivered'){
                DeliveryManTransaction::create([
                    'delivery_man_id' => $order->delivery_man_id,
                    'user_id' => 0,
                    'user_type' => 'admin',
                    'credit' => BackEndHelper::currency_to_usd($order->deliveryman_charge) ?? 0,
                    'transaction_id' => Uuid::uuid4(),
                    'transaction_type' => 'deliveryman_charge'
                ]);
            }
        }

        self::add_order_status_history($request->id, 0, $request->order_status, 'admin');

        $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request->order_status);
        }

        if ($request->order_status == 'delivered' && $order['seller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'admin');
            OrderDetail::where('order_id', $order->id)->update(
                ['delivery_status'=>'delivered']
            );
        }

        return response()->json($request->order_status);
    }

    public function amount_date_update(Request $request){
        $field_name = $request->field_name;
        $field_val = $request->field_val;
        $user_id = 0;

        $order = Order::find($request->order_id);
        $order->$field_name = $field_val;

        try {
            DB::beginTransaction();

            if($field_name == 'expected_delivery_date'){
                self::add_expected_delivery_date_history($request->order_id, $user_id, $field_val, 'admin');
            }
            $order->save();

            DB::commit();
        }catch(\Exception $ex){
            DB::rollback();
            return response()->json(['status' => false], 403);
        }

        if($field_name == 'expected_delivery_date') {
            $fcm_token = isset($order->delivery_man) ? $order->delivery_man->fcm_token:null;
            $value = Helpers::order_status_update_message($field_name) . " ID: " . $order['id'];
            if(!empty($fcm_token)) {
                try {
                    if ($value != null) {
                        $data = [
                            'title' => translate('order'),
                            'description' => $value,
                            'order_id' => $order['id'],
                            'image' => '',
                        ];

                        if ($order->delivery_man_id) {
                            self::add_deliveryman_push_notification($data, $order->delivery_man_id);
                        }
                        Helpers::send_push_notif_to_device($fcm_token, $data);
                    }
                } catch (\Exception $e) {
                    Toastr::warning(\App\CPU\translate('Push notification failed for DeliveryMan!'));
                }
            }
        }

        return response()->json(['status' => true], 200);
    }

    public function payment_status(Request $request)
    {
        if ($request->ajax()) {
            $order = Order::find($request->id);

            if(!isset($order->customer))
            {
                return response()->json(['customer_status'=>0],200);
            }

            $order = Order::find($request->id);
            $order->payment_status = $request->payment_status;
            $order->save();
            $data = $request->payment_status;
            return response()->json($data);
        }
    }

    public function change_order_status(Request $request)
    {
        if ($request->status) {
            $product_names = $product_uuid = '';
            $order = Order::find($request->id);
            $email_templates = $this->getEmailTemplate('order-status-change');
            $order->order_status = $request->status;
            $order->save();
            $data = $request->order_status;
            $user = User::where('id',$order->customer_id)->first();
            $msg = "Your Order with order id #$request->id has been $request->order_status";
            $payload['order_id'] = $request->id;
            //$this->save_invoice($request->id);
            //$invoice_file_path = public_path('public/assets/orders/order_invoice_'.$request->id.'.pdf');
            $this->sendNotification($user->fcm_token ?? "",$msg,$payload);
            $order_attribute = $this->getOrderAttr($order->mac_ids);
            //$this->print_r($order_attribute);
            if(!empty($order_attribute['product_name']) && is_array($order_attribute['product_name'])){
                $product_names = implode(',',$order_attribute['product_name']);
            }
            if(!empty($order_attribute['uuid']) && is_array($order_attribute['uuid'])){
                $product_uuid = implode(',',$order_attribute['uuid']);
            }
            $userData['username'] = $user->name ?? "Keepr User";
            $userData['order_id'] = $request->id;
            $userData['product_name'] = $product_names;
            $userData['device_id'] = $product_uuid;
            $userData['qty'] = $order_attribute['total_orders'] ?? 0;
            $userData['total_price'] = $order->order_amount ?? "";
            $userData['company_name'] = 'Keepr';
            $userData['company_logo'] = '<img src="'.url('/public/public/company/Keepe_logo.png').'" />';
            $subject = $this->replacedEmailVariables($request->status,$email_templates->subject ?? "Order");
            $body = $this->replacedEmailVariables($request->status,$email_templates->body ?? "Order status has been changed",$userData);
            $this->sendEmail($order->customer->email ?? "", $subject, $body);
            return response()->json($data);
        }
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

    public function generate_invoice($id)
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
                foreach($mac_ids as $k => $val){
                    $total_orders += count($mac_ids[$k]['uuid']);
                    $prod = Product::select('name','thumbnail','purchase_price')->find($k);
                    $products[$k]['name'] = $prod->name ?? "";
                    $products[$k]['thumbnail'] = $prod->thumbnail ?? "";
                    $products[$k]['price'] = $prod->purchase_price ?? 0;
                    if(!empty($val)){
                        foreach($val['uuid'] as $k1 => $val1){ 
                            $products[$k]['mac_ids'][$k1]['uuid'] = $val1;
                            $products[$k]['mac_ids'][$k1]['major'] = $val['major'][$k1];
                            $products[$k]['mac_ids'][$k1]['minor'] = $val['minor'][$k1];
                        }
                    }

                    if(!empty($order->taxes)){
                        $taxes = json_decode($order->taxes,true);
                        if(!empty($taxes)){
                            $tax_info[$k] = $taxes;
                        }
                    }

                    if(!empty($order->shipping_method_id) && !empty($order->shipping_mode)){
                        $shipping = ShippingMethod::where(['id' => $order->shipping_method_id])->first();
                        $shipping_method_rates = ShippingMethodRates::select('normal_rate','express_rate')->where('shipping_id',$order->shipping_method_id)->where('country_code',$this->getCountryName($order->customer->country))->first();
                        $shipping_info[$k]['title'] = $shipping->title ?? "";
                        if($order->shipping_mode == 'normal_rate'){
                            $shipping_info[$k]['duration'] = $shipping->normal_duration ?? "";
                            $shipping_info[$k]['mode'] = 'Regular Rate';
                            $shipping_info[$k]['amount'] = $shipping_method_rates->normal_rate ?? 0;
                        }elseif($order->shipping_mode == 'express_rate'){
                            $shipping_info[$k]['duration'] = $shipping->express_duration ?? "";
                            $shipping_info[$k]['mode'] = 'Express Rate';
                            $shipping_info[$k]['amount'] = $shipping_method_rates->express_rate ?? 0;
                        }

                        if(!empty($shipping_info[$k]['amount'])){
                            $total_order_amount += $shipping_info[$k]['amount'];
                        }
                    }
                }
            }
        }

        $mpdf_view = View::make('admin-views.order.invoice',
            compact('order', 'company_phone','total_orders','products', 'company_name', 'company_email', 'company_web_logo','total_order_amount','shipping_info','tax_info')
        );

        //echo "<pre>"; print_r($mpdf_view); die;
    
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    /*
     *  Digital file upload after sell
     */
    public function digital_file_upload_after_sell(Request $request)
    {
        $request->validate([
            'digital_file_after_sell'    => 'required|mimes:jpg,jpeg,png,gif,zip,pdf'
        ], [
            'digital_file_after_sell.required' => 'Digital file upload after sell is required',
            'digital_file_after_sell.mimes' => 'Digital file upload after sell upload must be a file of type: pdf, zip, jpg, jpeg, png, gif.',
        ]);

        $order_details = OrderDetail::find($request->order_id);
        $order_details->digital_file_after_sell = ImageManager::update('product/digital-product/', $order_details->digital_file_after_sell, $request->digital_file_after_sell->getClientOriginalExtension(), $request->file('digital_file_after_sell'));

        if($order_details->save()){
            Toastr::success('Digital file upload successfully!');
        }else{
            Toastr::error('Digital file upload failed!');
        }
        return back();
    }

    public function inhouse_order_filter()
    {
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            session()->put('show_inhouse_orders', 0);
        } else {
            session()->put('show_inhouse_orders', 1);
        }
        return back();
    }
    public function update_deliver_info(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->delivery_type = 'third_party_delivery';
        $order->delivery_service_name = $request->delivery_service_name;
        $order->third_party_delivery_tracking_id = $request->third_party_delivery_tracking_id;
        $order->delivery_man_id = null;
        $order->deliveryman_charge = 0;
        $order->expected_delivery_date = null;
        $order->save();

        Toastr::success(\App\CPU\translate('updated_successfully!'));
        return back();
    }

    public function bulk_export_data(Request $request, $status)
    {
        $search = $request['search'];
        $filter = $request['filter'];
        $from = $request['from'];
        $to = $request['to'];

        if ($status != 'all') {
            $orders = Order::when($filter,function($q) use($filter){
                $q->when($filter == 'all', function($q){
                    return $q;
                })
                    ->when($filter == 'POS', function ($q){
                        $q->whereHas('details', function ($q){
                            $q->where('order_type', 'POS');
                        });
                    })
                    ->when($filter == 'admin' || $filter == 'seller', function($q) use($filter){
                        $q->whereHas('details', function ($query) use ($filter){
                            $query->whereHas('product', function ($query) use ($filter){
                                $query->where('added_by', $filter);
                            });
                        });
                    });
            })
                ->with(['customer'])->where(function($query) use ($status){
                    $query->orWhere('order_status',$status)
                        ->orWhere('payment_status',$status);
                });
        } else {
            $orders = Order::with(['customer'])
                ->when($filter,function($q) use($filter){
                    $q->when($filter == 'all', function($q){
                        return $q;
                    })
                        ->when($filter == 'POS', function ($q){
                            $q->whereHas('details', function ($q){
                                $q->where('order_type', 'POS');
                            });
                        })
                        ->when(($filter == 'admin' || $filter == 'seller'), function($q) use($filter){
                            $q->whereHas('details', function ($query) use ($filter){
                                $query->whereHas('product', function ($query) use ($filter){
                                    $query->where('added_by', $filter);
                                });
                            });
                        });
                });
        }

        $key = $request['search'] ? explode(' ', $request['search']) : '';
        $orders = $orders->when($request->has('search') && $search!=null,function ($q) use ($key) {
            $q->where(function($qq) use ($key){
                foreach ($key as $value) {
                    $qq->where('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%");
                }});
        })->when(!empty($from) && !empty($to), function($dateQuery) use($from, $to) {
            $dateQuery->whereDate('created_at', '>=',$from)
                ->whereDate('created_at', '<=',$to);
        })->orderBy('id', 'DESC')->get();

        if ($orders->count()==0) {
            Toastr::warning(\App\CPU\translate('Data is Not available!!!'));
            return back();
        }

        $storage = array();

        foreach ($orders as $item) {

            $order_amount = $item->order_amount;
            $discount_amount = $item->discount_amount;
            $shipping_cost = $item->shipping_cost;
            $extra_discount = $item->extra_discount;

            $storage[] = [
                'order_id'=>$item->id,
                'Customer Id' => $item->customer_id,
                'Customer Name'=> isset($item->customer) ? $item->customer->f_name. ' '.$item->customer->l_name:'not found',
                'Order Group Id' => $item->order_group_id,
                'Order Status' => $item->order_status,
                'Order Amount' => Helpers::currency_converter($order_amount),
                'Order Type' => $item->order_type,
                'Coupon Code' => $item->coupon_code,
                'Discount Amount' => Helpers::currency_converter($discount_amount),
                'Discount Type' => $item->discount_type,
                'Extra Discount' => Helpers::currency_converter($extra_discount),
                'Extra Discount Type' => $item->extra_discount_type,
                'Payment Status' => $item->payment_status,
                'Payment Method' => $item->payment_method,
                'Transaction_ref' => $item->transaction_ref,
                'Verification Code' => $item->verification_code,
                'Billing Address' => isset($item->billingAddress)? $item->billingAddress->address:'not found',
                'Billing Address Data' => $item->billing_address_data,
                'Shipping Type' => $item->shipping_type,
                'Shipping Address' => isset($item->shippingAddress)? $item->shippingAddress->address:'not found',
                'Shipping Method Id' => $item->shipping_method_id,
                'Shipping Method Name' => isset($item->shipping)? $item->shipping->title:'not found',
                'Shipping Cost' => Helpers::currency_converter($shipping_cost),
                'Seller Id' => $item->seller_id,
                'Seller Name' => isset($item->seller)? $item->seller->f_name. ' '.$item->seller->l_name:'not found',
                'Seller Email'  => isset($item->seller)? $item->seller->email:'not found',
                'Seller Phone'  => isset($item->seller)? $item->seller->phone:'not found',
                'Seller Is' => $item->seller_is,
                'Shipping Address Data' => $item->shipping_address_data,
                'Delivery Type' => $item->delivery_type,
                'Delivery Man Id' => $item->delivery_man_id,
                'Delivery Service Name' => $item->delivery_service_name,
                'Third Party Delivery Tracking Id' => $item->third_party_delivery_tracking_id,
                'Checked' => $item->checked,

            ];
        }

        return (new FastExcel($storage))->download('Order_All_details.xlsx');
    }
}
