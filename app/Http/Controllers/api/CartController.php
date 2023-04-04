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
use App\Model\CheckoutInfo;
use App\Model\Shop;
use App\User;
use Illuminate\Support\Str;
use App\Model\ShippingType;
use App\Model\CategoryShippingCost;
use Illuminate\Http\Request;
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
                $cart[$key]['total_current_stock'] = ProductStock::where('product_id',$value['product_id'])->count() ?? 0;
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
        $current_stock = ProductStock::where('product_id',$request->product_id)->count();
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
        $mac_ids_array = [];
        $device_ids = [];
        $existed_mac_ids = [];
        $total_order = 0;
        $total_price = 0;
        $error = 0;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){

            $check_mac_ids = CheckoutInfo::select('mac_ids')->get();
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

            $cart_info = Cart::select('id','customer_id','product_id','quantity','name','thumbnail')->where('customer_id',$user_details->id)->where('quantity','>',0)->get();
            if(!empty($cart_info)){
                foreach($cart_info as $k => $cart){
                    $total_order += $cart['quantity'];
                    $price = Product::select('purchase_price as price')->where('id',$cart['product_id'])->first()->price ?? 0;
                    $total_price += ($price * $cart['quantity']);
                    $cart['purchase_price'] = number_format($price,2);

                    $get_random_stocks = ProductStock::select('mac_id','product_id')->where('product_id',$cart['product_id'])->whereNotIn('mac_id',$existed_mac_ids)
                                                      ->inRandomOrder()->limit($cart['quantity'])->get()->toArray();
                    if(!empty($get_random_stocks)){
                        foreach($get_random_stocks as $m => $macid){
                            $mac_ids_array[$cart['product_id']][$m] = $macid['mac_id'];
                        }
                    }

                    array_push($device_ids,$cart['product_id']);

                    //echo "<pre>"; print_r($get_random_stocks);

                    // if(!in_array($cart['product_id'],array_keys($mac_ids_array))){
                    //     $error = 1;
                    // }

                }

            }

            // if($error == 1){
            //     return response()->json(['status'=>400,'message'=>'Device not available'],400);
            // }
            
            // echo "<pre>"; print_r($error); die;
            // die;

            CheckoutInfo::insert(['product_id'=>json_encode($device_ids),'customer_id'=>$user_details->id,'mac_ids'=>json_encode($mac_ids_array),'total_order'=>$total_order,'total_amount'=>$total_price,'tax_amount'=>7]);

            $shipping = number_format(8,2);
            $tax = number_format(7,2);

            $data['cart_info'] = $cart_info;
            //$data['mac_ids'] = $mac_ids;
            $data['customer_id'] = $user_details->id;
            $data['total_order'] = $total_order;
            $data['sub_total'] = number_format($total_price,2);
            $data['shipping'] = $shipping;
            $data['tax'] = $tax;
            $data['total'] = number_format(($total_price + $shipping + $tax),2);
            //echo "<pre>"; print_r($mac_ids); die;

            return response()->json(['status'=>200,'message'=>'Success','data'=>$data],200);
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }

        // here i will add stripe tax api and calculate price based on no of device and will send in response
    }

    public function confirm_order(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        
        return response()->json(['status'=>200,'message'=>'Success'],200);
    }
    
}
