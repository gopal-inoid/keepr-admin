<?php

namespace App\Http\Controllers\api;

use App\CPU\CategoryManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\ProductStock;
use App\Model\Review;
use App\Model\ConnectedDevice;
use App\Model\DeviceTracking;
use App\Model\DeviceRequest;
use App\Model\ShippingMethod;
use App\Model\Wishlist;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;

class ProductController extends Controller
{
    public function get_latest_products(Request $request)
    {
        $products = ProductManager::get_latest_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_featured_products(Request $request)
    {
        $products = ProductManager::get_featured_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_top_rated_products(Request $request)
    {
        $products = ProductManager::get_top_rated_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_searched_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $products = ProductManager::search_products($request['name'], $request['limit'], $request['offset']);
        if ($products['products'] == null) {
            $products = ProductManager::translated_product_search($request['name'], $request['limit'], $request['offset']);
        }
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    public function get_product($slug)
    {
        $product = Product::with(['reviews.customer'])->where(['slug' => $slug])->first();
        if (isset($product)) {
            $product = Helpers::product_data_formatting($product, false);

            if(isset($product->reviews) && !empty($product->reviews)){
                $overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews);
                $product['average_review'] = $overallRating[0];
            }else{
                $product['average_review'] = 0;
            }


        }
        return response()->json($product, 200);
    }

    public function get_best_sellings(Request $request)
    {
        $products = ProductManager::get_best_selling_products($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);

        return response()->json($products, 200);
    }

    public function get_home_categories()
    {
        $categories = Category::where('home_status', true)->get();
        $categories->map(function ($data) {
            $data['products'] = Helpers::product_data_formatting(CategoryManager::products($data['id']), true);
            return $data;
        });
        return response()->json($categories, 200);
    }

    public function get_related_products($id)
    {
        if (Product::find($id)) {
            $products = ProductManager::get_related_products($id);
            $products = Helpers::product_data_formatting($products, true);
            return response()->json($products, 200);
        }
        return response()->json([
            'errors' => ['code' => 'product-001', 'message' => translate('Product not found!')]
        ], 404);
    }

    public function get_product_reviews($id)
    {
        $reviews = Review::with(['customer'])->where(['product_id' => $id])->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }

    public function get_product_rating($id)
    {
        try {
            $product = Product::find($id);
            $overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews);
            return response()->json(floatval($overallRating[0]), 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function counter($product_id)
    {
        try {
            $countOrder = OrderDetail::where('product_id', $product_id)->count();
            $countWishlist = Wishlist::where('product_id', $product_id)->count();
            return response()->json(['order_count' => $countOrder, 'wishlist_count' => $countWishlist], 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function social_share_link($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();
        $link = route('product', $product->slug);
        try {

            return response()->json($link, 200);
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
    }

    public function submit_product_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'comment' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $image_array = [];
        if (!empty($request->file('fileUpload'))) {
            foreach ($request->file('fileUpload') as $image) {
                if ($image != null) {
                    array_push($image_array, ImageManager::upload('review/', 'png', $image));
                }
            }
        }

        Review::updateOrCreate(
            [
                'delivery_man_id'=> null,
                'customer_id'=>$request->user()->id,
                'product_id'=>$request->product_id
            ],
            [
                'customer_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'comment' => $request->comment,
                'rating' => $request->rating,
                'attachment' => json_encode($image_array),
            ]
        );

        return response()->json(['message' => translate('successfully review submitted!')], 200);
    }

    public function submit_deliveryman_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'comment' => 'required',
            'rating' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = Order::where([
                'id'=>$request->order_id,
                'customer_id'=>$request->user()->id,
                'payment_status'=>'paid'])->first();

        if(!isset($order->delivery_man_id)){
            return response()->json(['message' => translate('Invalid review!')], 403);
        }

        Review::updateOrCreate(
            [
                'delivery_man_id'=>$order->delivery_man_id,
                'customer_id'=>$request->user()->id,
                'order_id' => $order->id
            ],
            [
                'customer_id' => $request->user()->id,
                'order_id' => $order->id,
                'delivery_man_id' => $order->delivery_man_id,
                'comment' => $request->comment,
                'rating' => $request->rating,
            ]
        );

        return response()->json(['message' => translate('successfully review submitted!')], 200);
    }

    public function get_shipping_methods(Request $request)
    {
        $methods = ShippingMethod::where(['status' => 1])->get();
        return response()->json($methods, 200);
    }

    public function get_discounted_product(Request $request)
    {
        $products = ProductManager::get_discounted_product($request['limit'], $request['offset']);
        $products['products'] = Helpers::product_data_formatting($products['products'], true);
        return response()->json($products, 200);
    }

    //START DEVICE API's
    public function connect_device(Request $request){
        $device_uuid = $request->uuid;
        $device_mac_id = $request->mac_id;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $check_connected = ConnectedDevice::select('id')->where(['mac_id'=>$device_mac_id,'user_id'=>$user_details->id])->first();
            if(!empty($check_connected->id)){
                return response()->json(['status'=>400,'message'=>'Device already connected'],400);
            }
            $device_info = ProductStock::where('mac_id',$device_mac_id)->first();
            //$device_info = Product::select('name')->where('mac_id',$device_mac_id)->first();
            if(!empty($device_info->mac_id)){
                $check = ConnectedDevice::insert(['device_name'=>$device_info->mac_id,'mac_id'=>$device_mac_id,'user_id'=>$user_details->id,'device_uuid'=>$device_uuid]);
                if($check){
                    return response()->json(['status'=>200,'message'=>'Device connected successfully'],200);
                }
            }else{
                return response()->json(['status'=>400,'message'=>'Device not found'],400);
            }
        }

        return response()->json(['status'=>400,'message'=>'Something Went Wrong, Please try again latter'],400);
    }

    public function edit_device(Request $request){
        $name = $request->name;
        $mac_id = $request->mac_id;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $check = ConnectedDevice::where(['mac_id'=>$mac_id,'user_id'=>$user_details->id])->update(['device_name'=>$name]);
            if($check){
                return response()->json(['status'=>200,'message'=>'Device name updated successfully'],200);
            }
        }

        return response()->json(['status'=>400,'message'=>'Something Went Wrong, Please try again latter'],400);
    }

    public function delete_device(Request $request){
        $mac_id = $request->mac_id;
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $check = ConnectedDevice::where(['mac_id'=>$mac_id,'user_id'=>$user_details->id])->delete();
            if($check){
                return response()->json(['status'=>200,'message'=>'Device deleted successfully'],200);
            }
        }

        return response()->json(['status'=>400,'message'=>'Something Went Wrong, Please try again latter'],400);
    }

    public function get_connected_device(Request $request){
        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $get_all_devices = ConnectedDevice::where(['user_id'=>$user_details->id,'status'=>1])->get();
            if(!empty($get_all_devices)){
                return response()->json(['status'=>200,'message'=>'Success','data'=>$get_all_devices],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Devices not found'],400);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function all_available_devices(){
        $devices_list = Product::select('id','device_id','name','details','purchase_price','thumbnail')->where(['status'=>1])->get();
        if(!empty($devices_list)){
            foreach($devices_list as $k => $devices){
                if(!empty($devices->thumbnail)){
                    $devices->thumbnail = asset("/product/thumbnail/$devices->thumbnail");
                }else{
                    $devices->thumbnail = asset('public/assets/front-end/img/image-place-holder.png');
                }
            }
            return response()->json(['status'=>200,'message'=>'Success','data'=>$devices_list],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Devices not found'],400);
        }
    }

    public function devices_type_list(){
        $devices_list = Product::select('device_id','name','thumbnail')->where(['status'=>1])->get();
        if(!empty($devices_list)){
            foreach($devices_list as $k => $devices){
                if(!empty($devices->thumbnail)){
                    $devices->thumbnail = asset("/product/thumbnail/$devices->thumbnail");
                }else{
                    $devices->thumbnail = asset('public/assets/front-end/img/image-place-holder.png');
                }
            }
            return response()->json(['status'=>200,'message'=>'Success','data'=>$devices_list],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Devices not found'],400);
        }
    }

    public function search_device(Request $request){
        $keyword = $request->keyword;
        $devices_list = Product::select('id','name','images','thumbnail','details','specification','faq')->where('status',1);
        if(!empty($keyword)){
            $devices_list = $devices_list->whereRaw('name like "%'.$keyword.'%"');
        }
        $devices_list = $devices_list->get();
        if(!empty($devices_list[0])){
            return response()->json(['status'=>200,'message'=>'Success','data'=>$devices_list],200);
        }else{
            return response()->json(['status'=>400,'message'=>'Devices not found'],400);
        }
    }

    public function get_device_detail(Request $request){
        $mac_id = $request->mac_id;
        $device_info = ProductStock::select('product_id')->where('mac_id',$mac_id)->first();
        $devices_details = [];
        if(!empty($device_info->product_id)){
            $devices_details = Product::where(['status'=>1,'id'=>$device_info->product_id])->first();
            if(!empty($devices_details->id)){
                return response()->json(['status'=>200,'message'=>'Success','data'=>$devices_details],200);
            }else{
                return response()->json(['status'=>400,'message'=>'Devices not found'],400);
            }
        }
    }

    public function device_tracking(Request $request){
        $device_mac_id = $request->mac_id;
        $lat = $request->lat;
        $lan = $request->lan;

        $validator = Validator::make($request->all(), [
            'mac_id' => 'required',
            'lat' => 'required',
            'lan' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $check_connected = DeviceTracking::select('id')->where(['mac_id'=>$device_mac_id,'user_id'=>$user_details->id])->first();
            if(!empty($check_connected->id)){
                return response()->json(['status'=>400,'message'=>'Device already added in tracking'],400);
            }
            $device_info = ProductStock::where('mac_id',$device_mac_id)->first();
            if(!empty($device_info->mac_id)){
                $check = DeviceTracking::insert(['mac_id'=>$device_mac_id,'user_id'=>$user_details->id,'lat'=>$lat,'lan'=>$lan]);
                if($check){
                    return response()->json(['status'=>200,'message'=>'Device successfully added in tracking'],200);
                }
            }else{
                return response()->json(['status'=>400,'message'=>'Device not found'],400);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    public function request_device(Request $request){
        $device_mac_id = $request->mac_id;
        $validator = Validator::make($request->all(), [
            'mac_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $auth_token   = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token'=>$auth_token])->first();
        if(!empty($user_details->id)){
            $device_info = ProductStock::where('mac_id',$device_mac_id)->first();
            if(!empty($device_info->mac_id)){
                $check_connected = DeviceRequest::select('id','status','last_updated')->where(['mac_id'=>$device_mac_id,'user_id'=>$user_details->id])->first();
                if(!empty($check_connected->id)){
                    if($check_connected->status == 1){
                        $check_connected->status = 0;
                    }else{
                        $check_connected->status = 1;
                    }
                    //$check_connected->last_updated = date('Y-m-d h:i:s');
                    $check_connected->save();

                    return response()->json(['status'=>200,'status'=>$check_connected->status,'message'=>'Device request updated successfully'],200);

                }else{
                    $check = DeviceRequest::insert(['mac_id'=>$device_mac_id,'user_id'=>$user_details->id]);
                    if($check){
                        return response()->json(['status'=>200,'message'=>'Device lost request added successfully'],200);
                    }
                }
            }else{
                return response()->json(['status'=>400,'message'=>'Device not found'],400);
            }
        }else{
            return response()->json(['status'=>400,'message'=>'User not found'],400);
        }
    }

    //END DEVICE API's

}
