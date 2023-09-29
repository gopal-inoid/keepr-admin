<?php

namespace App\Http\Controllers\api;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\Admin;
use App\Model\Cart;
use App\Model\Order;
use App\User;
use App\Model\OrderDetail;
use App\Model\Seller;
use App\Model\ShippingAddress;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\RefundRequest;
use App\CPU\ImageManager;
use App\Model\DeliveryMan;
use App\CPU\CustomerManager;
use App\Model\ShippingMethodRates;
use App\Model\ShippingMethod;

class OrderController extends Controller
{
    use CommonTrait;
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderManager::track_order($request['order_id']), 200);
    }
    public function order_cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::where(['id' => $request->order_id])->first();

        if ($order['payment_method'] == 'cash_on_delivery' && $order['order_status'] == 'pending') {
            OrderManager::stock_update_on_order_status_change($order, 'canceled');
            Order::where(['id' => $request->order_id])->update([
                'order_status' => 'canceled'
            ]);

            return response()->json(translate('order_canceled_successfully'), 200);
        }

        return response()->json(translate('status_not_changable_now'), 302);
    }
    public function place_order(Request $request)
    {
        $cart_group_ids = CartManager::get_cart_group_ids($request);
        $carts = Cart::whereIn('cart_group_id', $cart_group_ids)->get();

        $physical_product = false;
        foreach ($carts as $cart) {
            if ($cart->product_type == 'physical') {
                $physical_product = true;
            }
        }

        if ($physical_product) {
            $zip_restrict_status = Helpers::get_business_settings('delivery_zip_code_area_restriction');
            $country_restrict_status = Helpers::get_business_settings('delivery_country_restriction');

            if ($request->has('billing_address_id')) {
                $shipping_address = ShippingAddress::where(['customer_id' => $request->user()->id, 'id' => $request->input('billing_address_id')])->first();

                if (!$shipping_address) {
                    return response()->json(['message' => translate('address_not_found')], 200);
                } elseif ($country_restrict_status && !self::delivery_country_exist_check($shipping_address->country)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_country')], 403);

                } elseif ($zip_restrict_status && !self::delivery_zipcode_exist_check($shipping_address->zip)) {
                    return response()->json(['message' => translate('Delivery_unavailable_for_this_zip_code_area')], 403);
                }
            }
        }


        $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
        $order_ids = [];
        foreach ($cart_group_ids as $group_id) {
            $data = [
                'payment_method' => 'cash_on_delivery',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'transaction_ref' => '',
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id,
                'request' => $request,
            ];
            $order_id = OrderManager::generate_order($data);

            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ? ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            $order->save();

            array_push($order_ids, $order_id);
        }

        CartManager::cart_clean($request);

        return response()->json(translate('order_placed_successfully'), 200);
    }
    public function refund_request(Request $request)
    {
        $order_details = OrderDetail::find($request->order_details_id);

        $user = $request->user();


        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($request->order_details_id);

            if ($user->loyalty_point < $loyalty_point) {
                return response()->json(['message' => translate('you have not sufficient loyalty point to refund this order!!')], 202);
            }
        }

        if ($order_details->delivery_status == 'delivered') {
            $order = Order::find($order_details->order_id);
            $total_product_price = 0;
            $refund_amount = 0;
            $data = [];
            foreach ($order->details as $key => $or_d) {
                $total_product_price += ($or_d->qty * $or_d->price) + $or_d->tax - $or_d->discount;
            }

            $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

            $coupon_discount = ($order->discount_amount * $subtotal) / $total_product_price;

            $refund_amount = $subtotal - $coupon_discount;

            $data['product_price'] = $order_details->price;
            $data['quntity'] = $order_details->qty;
            $data['product_total_discount'] = $order_details->discount;
            $data['product_total_tax'] = $order_details->tax;
            $data['subtotal'] = $subtotal;
            $data['coupon_discount'] = $coupon_discount;
            $data['refund_amount'] = $refund_amount;

            $refund_day_limit = Helpers::get_business_settings('refund_day_limit');
            $order_details_date = $order_details->created_at;
            $current = \Carbon\Carbon::now();
            $length = $order_details_date->diffInDays($current);
            $expired = false;
            $already_requested = false;
            if ($order_details->refund_request != 0) {
                $already_requested = true;
            }
            if ($length > $refund_day_limit) {
                $expired = true;
            }
            return response()->json(['already_requested' => $already_requested, 'expired' => $expired, 'refund' => $data], 200);
        } else {
            return response()->json(['message' => translate('You_can_request_for_refund_after_order_delivered')], 200);
        }

    }
    public function store_refund(Request $request)
    {

        $order_details = OrderDetail::find($request->order_details_id);

        $user = $request->user();


        $loyalty_point_status = Helpers::get_business_settings('loyalty_point_status');
        if ($loyalty_point_status == 1) {
            $loyalty_point = CustomerManager::count_loyalty_point_for_amount($request->order_details_id);

            if ($user->loyalty_point < $loyalty_point) {
                return response()->json(translate('you have not sufficient loyalty point to refund this order!!'), 200);
            }
        }

        if ($order_details->refund_request == 0) {

            $validator = Validator::make($request->all(), [
                'order_details_id' => 'required',
                'amount' => 'required',
                'refund_reason' => 'required'

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $refund_request = new RefundRequest;
            $refund_request->order_details_id = $request->order_details_id;
            $refund_request->customer_id = $request->user()->id;
            $refund_request->status = 'pending';
            $refund_request->amount = $request->amount;
            $refund_request->product_id = $order_details->product_id;
            $refund_request->order_id = $order_details->order_id;
            $refund_request->refund_reason = $request->refund_reason;

            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('refund/', 'png', $img);
                }
                $refund_request->images = json_encode($product_images);
            }
            $refund_request->save();

            $order_details->refund_request = 1;
            $order_details->save();

            return response()->json(translate('refunded_request_updated_successfully!!'), 200);
        } else {
            return response()->json(translate('already_applied_for_refund_request!!'), 302);
        }

    }
    public function refund_details(Request $request)
    {
        $order_details = OrderDetail::find($request->id);
        $refund = RefundRequest::where('customer_id', $request->user()->id)
            ->where('order_details_id', $order_details->id)->get();
        $refund = $refund->map(function ($query) {
            $query['images'] = json_decode($query['images']);
            return $query;
        });

        $order = Order::find($order_details->order_id);

        $total_product_price = 0;
        $refund_amount = 0;
        $data = [];
        foreach ($order->details as $key => $or_d) {
            $total_product_price += ($or_d->qty * $or_d->price) + $or_d->tax - $or_d->discount;
        }

        $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

        $coupon_discount = ($order->discount_amount * $subtotal) / $total_product_price;

        $refund_amount = $subtotal - $coupon_discount;

        $data['product_price'] = $order_details->price;
        $data['quntity'] = $order_details->qty;
        $data['product_total_discount'] = $order_details->discount;
        $data['product_total_tax'] = $order_details->tax;
        $data['subtotal'] = $subtotal;
        $data['coupon_discount'] = $coupon_discount;
        $data['refund_amount'] = $refund_amount;
        $data['refund_request'] = $refund;

        return response()->json($data, 200);
    }

    public function update_order_details(Request $request)
    {
        $order_id = $request->order_id;
        $user_id = $request->user_id;
        if (!empty($order_id)) {
            $user_details = User::where(['id' => $user_id])->first();
            if (!empty($user_id)) {
                $user_details = User::where(['id' => $user_id])->first();
                $user_data['name'] = $request->billing_name;
                $user_data['email'] = $request->email;
                $user_data['street_address'] = $request->street_address;
                $user_data['city'] = $request->billing_city;
                $user_data['state'] = $request->billing_state;
                $user_data['country'] = $request->billing_country;
                $user_data['zip'] = $request->billing_zip;
                $user_data['billing_phone'] = $request->billing_phone;
                $user_data['billing_phone_code'] = $request->billing_phone_code;
                $user_data['shipping_name'] = $request->shipping_name;
                $user_data['shipping_email'] = $request->shipping_email;
                $user_data['add_shipping_address'] = $request->add_shipping_address;
                $user_data['shipping_city'] = $request->shipping_city;
                $user_data['shipping_state'] = $request->shipping_state;
                $user_data['shipping_country'] = $request->shipping_country;
                $user_data['shipping_zip'] = $request->shipping_zip;
                $user_data['shipping_phone'] = $request->shipping_phone;
                $user_data['shipping_phone_code'] = $request->shipping_phone_code;
                if (!empty($request->is_billing_address_same) && $request->is_billing_address_same == 'on') {
                    $user_data['is_billing_address_same'] = 1;
                }
                User::where('id', $user_id)->update($user_data);
            }

            $order_data['order_status'] = $request->change_order_status;
            $order_data['created_at'] = date('Y-m-d h:i:s', strtotime($request->order_date));
            $order_data['order_note'] = $request->order_note;
            $order_data['expected_delivery_date'] = date('Y-m-d h:i:s', strtotime($request->expected_delivery_date));
            $order_data['shipment_info'] = $request->shipment_info;
            $order_data['transaction_ref'] = $request->transaction_ref;
            $order_data['payment_method'] = $request->payment_method;
            $order_data['payment_status'] = $request->payment_status;
            $order_data['tracking_id'] = $request->tracking_id;
            $order_data['shipping_mode'] = $request->shipping_mode;
      
            //Send Email code
            $userData = $user_data+$this->getDataforEmail($order_id);
            if (!empty($userData)) {
                $userData['username'] = $user_details['name'] ?? "Keepr User";
                $userData['email'] = $user_details->email ?? "";
                if ($userData['order_status'] == 'shipped') {
                    $userData['email'] = $user_details->shipping_email ?? "";
                    $this->sendKeeprEmail('order-shipped-customer', $userData);
                } elseif ($userData['order_status'] == 'cancelled') {
                    $this->sendKeeprEmail('order-cancelled-customer', $userData);
                } elseif ($userData['order_status'] == 'refunded') {
                    $this->sendKeeprEmail('order-refunded-customer', $userData);
                } elseif ($userData['order_status'] == 'delivered') {
                    $this->sendKeeprEmail('order-delivered-customer', $userData);
                } else {
                    $this->sendKeeprEmail('order-status-changed-customer', $userData);
                }
                $userData['username'] = $this->getAdminDetail('company_name') ?? "Keepr Admin";
                $userData['email'] = $this->getAdminDetail('company_email') ?? "";
                $this->sendKeeprEmail('order-status-changed-admin', $userData);
                Order::where('id', $order_id)->update($order_data);
                return response()->json(['status' => 200, 'message' => 'Order Status Successfully Changed'], 200);
            }
        } else {
            return response()->json(['status' => 400, 'message' => 'Order Status Change failed'], 200);
        }

    }
}