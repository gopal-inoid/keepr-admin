<?php

namespace App\Http\Controllers\api;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\Color;
use App\Model\Product;
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

    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mac_id' => 'required',
            'quantity' => 'required',
        ], [
            'mac_id.required' => translate('MAC ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $product = Product::find($request->id);
        $cart = Cart::where(['product_id' => $request->id, 'customer_id' => $user_details->id])->first();
        if (isset($cart) == false) {
            $cart = new Cart();
        } else {
            return response()->json([
                'status' => 0,
                'message' => translate('already_added!')
            ], 200);
        }

        if ($product['current_stock'] < $request['quantity']) {
            return response()->json([
                'status' => 0,
                'message' => translate('out_of_stock!')
            ], 200);
        }

        $price = $product->unit_price;
        $tax = Helpers::tax_calculation($price, $product['tax'], 'percent');
        $cart['customer_id'] = $user_details->id ?? 0;
        $cart['quantity'] = $request['quantity'];
        $cart['price'] = $price;
        $cart['tax'] = $tax;
        $cart['name'] = $product->name;
        $cart['discount'] = Helpers::get_product_discount($product, $price);
        $cart['thumbnail'] = $product->thumbnail;
        $cart->save();
        //$cart = CartManager::add_to_cart($request);
        return response()->json([
            'status' => 1,
            'message' => translate('successfully_added!')
        ], 200);
    }

    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ], [
            'id.required' => translate('Cart ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $status = 1;
        $qty = 0;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        $cart = Cart::where(['id' => $request->id, 'customer_id' => $user_details->id])->first();
        $product = Product::find($cart['product_id']);
        if ($product['current_stock'] < $request->quantity) {
            $status = 0;
            $qty = $cart['quantity'];
        }

        if ($status) {
            $qty = $request->quantity;
            $cart['quantity'] = $request->quantity;
        }
        $cart->save();
        return response()->json([
            'status' => $status,
            'qty' => $qty,
            'message' => $status == 1 ? translate('successfully_updated!') : translate('sorry_stock_is_limited')
        ],200);
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

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        Cart::where(['id' => $request->id, 'customer_id' => $user_details->id])->delete();
        return response()->json(translate('successfully_removed'));
    }
    public function remove_all_from_cart(Request $request)
    {
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        Cart::where(['customer_id' => $user_details->id])->delete();
        return response()->json(translate('successfully_removed'));
    }
}
