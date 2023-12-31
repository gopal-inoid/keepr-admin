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
use App\Model\Color;
use App\Model\Review;
use App\Model\ConnectedDevice;
use App\Model\Cart;
use App\Model\DeviceTracking;
use App\Model\DeviceRequest;
use App\Model\ShippingMethod;
use App\Model\Wishlist;
use App\User;
use App\Common;
use Carbon\Carbon;
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

            if (isset($product->reviews) && !empty($product->reviews)) {
                $overallRating = \App\CPU\ProductManager::get_overall_rating($product->reviews);
                $product['average_review'] = $overallRating[0];
            } else {
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
                'delivery_man_id' => null,
                'customer_id' => $request->user()->id,
                'product_id' => $request->product_id
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
            'id' => $request->order_id,
            'customer_id' => $request->user()->id,
            'payment_status' => 'paid'
        ])->first();

        if (!isset($order->delivery_man_id)) {
            return response()->json(['message' => translate('Invalid review!')], 403);
        }

        Review::updateOrCreate(
            [
                'delivery_man_id' => $order->delivery_man_id,
                'customer_id' => $request->user()->id,
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
    public function connect_device(Request $request)
    {
        $device_uuid = $request->uuid;
        $device_mac_id = $request->mac_id;
        $distance = $request->distance;
        $major = $request->major;
        $minor = $request->minor;
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $check_connected = ConnectedDevice::select('id')->where(['device_uuid' => $device_uuid, 'major' => $major, 'minor' => $minor])->first();
            if (!empty($check_connected->id)) {
                Common::addLog(['status' => 400, 'message' => 'You cannot connect to this device, it is already connected with another user.']);
                return response()->json(['status' => 400, 'message' => 'You cannot connect to this device, it is already connected with another user.'], 400);
            }
            $device_info = ProductStock::select('products.id as pro_id', 'products.name')->join('products', 'product_stocks.product_id', 'products.id')
                ->where(['product_stocks.uuid' => $device_uuid, 'product_stocks.major' => $major, 'product_stocks.minor' => $minor])->first();
            if (!empty($device_info->pro_id)) {
                $check = ConnectedDevice::insert(['device_name' => $device_info->name, 'mac_id' => $device_mac_id, 'user_id' => $user_details->id, 'device_uuid' => $device_uuid, 'distance' => $distance, 'major' => $major, 'minor' => $minor]);
                if ($check) {
                    $user_order = Order::where('customer_id', $user_details->id)->where('order_status', 'shipped')->orderBy('id', 'asc')->first();
                    if (!empty($user_order->product_info)) {
                        $mac_ids_array = $existed_mac_ids = [];
                        $product_info = json_decode($user_order->product_info, true);
                        if (!empty($product_info)) {

                            // if(!empty($user_order['mac_ids'])){
                            //     $mac_id_arr = json_decode($user_order['mac_ids'], true);
                            //     if (!empty($mac_id_arr)) {
                            //         foreach ($mac_id_arr as $product_id => $mac_values) {
                            //             foreach ($mac_values as $k => $mac_ids) {
                            //                 $existed_mac_ids[$k][] = $mac_ids;
                            //             }
                            //         }
                            //     }
                            // }

                            foreach ($product_info as $k => $val) {
                                $check_stock = ProductStock::select('mac_id', 'uuid', 'major', 'minor', 'product_id')->where('is_purchased', 0)
                                    ->where(['product_id' => $k, 'uuid' => $device_uuid, 'major' => $major, 'minor' => $minor])->first();
                                if (!empty($check_stock->product_id)) {
                                    $mac_ids_array[$k]['device_id'][] = $check_stock->mac_id;
                                    $mac_ids_array[$k]['uuid'][] = $device_uuid;
                                    $mac_ids_array[$k]['major'][] = $major;
                                    $mac_ids_array[$k]['minor'][] = $minor;
                                    ProductStock::where('product_id', $k)->where(['uuid' => $device_uuid, 'major' => $major, 'minor' => $minor])->update(['is_purchased' => 1]);
                                }
                            }

                            $user_order->mac_id = json_encode($mac_ids_array);
                            $user_order->save();
                        }
                    }
                    Common::addLog(['status' => 200, 'message' => 'Device connected successfully']);
                    return response()->json(['status' => 200, 'message' => 'Device connected successfully'], 200);
                }
            } else {
                Common::addLog(['status' => 400, 'message' => 'Device not found']);
                return response()->json(['status' => 400, 'message' => 'Device not found'], 400);
            }

        }
        Common::addLog(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter']);
        return response()->json(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter'], 400);
    }

    public function edit_device(Request $request)
    {
        $name = $request->name;
        $mac_id = $request->mac_id;
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();

        if (!empty($user_details->id)) {
            $check = ConnectedDevice::where(['device_uuid' => $uuid, 'major' => $major, 'minor' => $minor, 'user_id' => $user_details->id])->update(['device_name' => $name]);
            if ($check) {
                Common::addLog(['status' => 200, 'message' => 'Device name updated successfully']);
                return response()->json(['status' => 200, 'message' => 'Device name updated successfully'], 200);
            }
        }
        Common::addLog(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter']);
        return response()->json(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter'], 400);
    }

    public function delete_device(Request $request)
    {
        $mac_id = $request->mac_id;
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $check = ConnectedDevice::where(['device_uuid' => $uuid, 'major' => $major, 'minor' => $minor, 'user_id' => $user_details->id])->delete();
            if ($check) {
                Common::addLog(['status' => 200, 'message' => 'Device deleted successfully']);
                return response()->json(['status' => 200, 'message' => 'Device deleted successfully'], 200);
            }
        }
        Common::addLog(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter']);
        return response()->json(['status' => 400, 'message' => 'Something Went Wrong, Please try again latter'], 400);
    }

    public function get_connected_device(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $get_all_devices = ConnectedDevice::where(['user_id' => $user_details->id, 'status' => 1])->get();
            if (!empty($get_all_devices)) {
                if (!empty($get_all_devices)) {
                    foreach ($get_all_devices as $k => $devices) {
                        $device_info = ProductStock::select('products.thumbnail', 'products.rssi', 'products.id as product_id', 'products.name as device_type')
                            ->join('products', 'products.id', 'product_stocks.product_id')
                            ->where([
                                'product_stocks.uuid' => $devices->device_uuid,
                                'product_stocks.major' => $devices->major,
                                'product_stocks.minor' => $devices->minor
                            ])
                            ->first();

                        $get_all_devices[$k]['rssi'] = $device_info->rssi ?? '';
                        $get_all_devices[$k]['device_id'] = !empty($device_info->product_id) ? (string) $device_info->product_id : "";
                        $get_all_devices[$k]['device_type'] = $device_info->device_type ?? '';
                        $get_all_devices[$k]['distance'] = "-1";

                        $device_request = DeviceRequest::select('status')->where([
                            'uuid' => $devices->device_uuid,
                            'major' => $devices->major,
                            'minor' => $devices->minor,
                            'user_id' => $user_details->id
                        ])->first();
                        $get_all_devices[$k]['device_request_status'] = $device_request->status ?? 2; // 0 = lost , 1 = found // 2 means not sent request
                        if (!empty($device_info->thumbnail)) {
                            $get_all_devices[$k]['thumbnail'] = asset("/product/thumbnail/$device_info->thumbnail");
                        } else {
                            $get_all_devices[$k]['thumbnail'] = asset('public/assets/front-end/img/image-place-holder.png');
                        }
                    }
                }
                Common::addLog(['status' => 200, 'message' => 'Success', 'data' => $get_all_devices]);
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $get_all_devices], 200);
            } else {
                Common::addLog(['status' => 400, 'message' => 'Devices not found']);
                return response()->json(['status' => 400, 'message' => 'Devices not found'], 400);
            }
        } else {
            Common::addLog(['status' => 400, 'message' => 'User not found']);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function all_available_devices(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();

        $devices_list = Product::select('products.colors', 'products.id', 'name', 'details', 'purchase_price', 'thumbnail', DB::raw('COUNT(product_stocks.id) as total_stocks'))
            ->Join('product_stocks', 'product_stocks.product_id', 'products.id')
            ->where('products.status', 1)
            ->groupBy('products.id')->get();

        if (!empty($devices_list)) {
            foreach ($devices_list as $k => $devices) {

                $colors_stocks = \DB::table('product_stocks')->select('color', DB::raw('COUNT(id) as total_stocks'))
                    ->where('product_id', $devices->id)->where('is_purchased', 0)->groupBy('color')->get();

                //echo "<pre>"; print_r($colors_stocks); die;

                if (!empty($devices->colors) && !empty($colors_stocks[0])) {
                    $devices->colors = $colors_stocks;
                }

                if (!empty($devices->thumbnail)) {
                    $devices->thumbnail = asset("/product/thumbnail/$devices->thumbnail");
                } else {
                    $devices->thumbnail = asset('public/assets/front-end/img/image-place-holder.png');
                }
                $devices->price = number_format($devices->purchase_price, 2);
                unset($devices->purchase_price);

            }
            $total_quantity = (int) Cart::where(['customer_id' => $user_details->id])->sum('quantity');
            Common::addLog(['status' => 200, 'message' => 'Success', 'total_quantity' => $total_quantity, 'data' => $devices_list]);
            return response()->json(['status' => 200, 'message' => 'Success', 'total_quantity' => $total_quantity, 'data' => $devices_list], 200);
        } else {
            Common::addLog(['status' => 400, 'message' => 'Devices not found']);
            return response()->json(['status' => 400, 'message' => 'Devices not found'], 400);
        }
    }

    public function devices_type_list()
    {
        $devices_list = Product::select('id', 'device_id', 'name', 'thumbnail', 'rssi', 'uuid')->where(['status' => 1])->get();
        if (!empty($devices_list)) {
            foreach ($devices_list as $k => $devices) {
                if (!empty($devices->thumbnail)) {
                    $devices->thumbnail = asset("/product/thumbnail/$devices->thumbnail");
                } else {
                    $devices->thumbnail = asset('public/assets/front-end/img/image-place-holder.png');
                }
            }
            Common::addLog(['status' => 200, 'message' => 'Success', 'data' => $devices_list]);
            return response()->json(['status' => 200, 'message' => 'Success', 'data' => $devices_list], 200);
        } else {
            Common::addLog(['status' => 400, 'message' => 'Devices not found']);
            return response()->json(['status' => 400, 'message' => 'Devices not found'], 400);
        }
    }

    public function search_device(Request $request)
    {
        $keyword = $request->keyword;
        $devices_list = Product::select('id', 'name', 'images', 'purchase_price', 'thumbnail', 'details')->where('status', 1);
        if (!empty($keyword)) {
            $devices_list = $devices_list->whereRaw('name like "%' . $keyword . '%"');
        }
        $devices_list = $devices_list->get();
        if (!empty($devices_list[0])) {
            foreach ($devices_list as $k => $devices) {
                $devices->thumbnail = asset("/product/thumbnail/$devices->thumbnail");
                $devices->price = number_format($devices->purchase_price, 2);
                unset($devices->purchase_price);
            }
            Common::addLog(['status' => 200, 'message' => 'Success', 'data' => $devices_list]);
            return response()->json(['status' => 200, 'message' => 'Success', 'data' => $devices_list], 200);
        } else {
            Common::addLog(['status' => 400, 'message' => 'Devices not found']);
            return response()->json(['status' => 400, 'message' => 'Devices not found'], 400);
        }
    }

    public function get_device_detail(Request $request)
    {
        $device_id = $request->id;
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            //$device_info = ProductStock::select('product_id')->where('mac_id',$mac_id)->first();
            $devices_details_array = [];
            $devices_details = Product::select('id', 'name', 'images', 'thumbnail', 'details', 'specification', 'faq', 'purchase_price', 'colors')
                ->where(['status' => 1, 'id' => $device_id])->first();
            if (!empty($devices_details->id)) {

                $colorStocks = Color::select('colors.name AS color', 'colors.code')
                    ->selectSub(function ($query) use ($devices_details) {
                        $query->selectRaw('COUNT(product_stocks.color) AS total_stocks')
                            ->from('product_stocks')
                            ->whereColumn('product_stocks.color', 'colors.id')
                            ->where("product_stocks.product_id", $devices_details->id)
                            ->where("product_stocks.is_purchased", 0);
                    }, 'total_stocks')
                    ->whereRaw("FIND_IN_SET(colors.id, '" . $devices_details->colors . "')")
                    ->groupBy('colors.id')
                    ->get();

                //echo "<pre>"; print_r($colorStocks); die;

                $devices_details_array['total_quantity'] = (int) Cart::where(['customer_id' => $user_details->id])->sum('quantity');
                $devices_details_array['id'] = $devices_details->id;
                $devices_details_array['name'] = $devices_details->name;
                $devices_details_array['details'] = $devices_details->details;
                $devices_details_array['thumbnail'] = asset("/product/thumbnail/$devices_details->thumbnail");
                $devices_details_array['price'] = number_format($devices_details->purchase_price, 2);
                if (!empty($devices_details->images)) {
                    $device_images = json_decode($devices_details->images);
                    if (!empty($device_images)) {
                        foreach ($device_images as $k => $val) {
                            $devices_details_array['images'][$k] = asset("/product/$val");
                        }
                    }
                }
                if (!empty($devices_details->specification)) {
                    $devices_details_array['specification'] = json_decode($devices_details->specification, true);
                }
                if (!empty($devices_details->faq)) {
                    $devices_details_array['faq'] = json_decode($devices_details->faq, true);
                }
                if (!empty($devices_details->colors) && !empty($colorStocks)) {
                    $devices_details_array['colors'] = $colorStocks;
                }
                Common::addLog(['status' => 200, 'message' => 'Success', 'data' => $devices_details_array]);
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $devices_details_array], 200);
            } else {
                Common::addLog(['status' => 400, 'message' => 'Devices not found']);
                return response()->json(['status' => 400, 'message' => 'Devices not found'], 400);
            }
        } else {
            Common::addLog(['status' => 400, 'message' => 'User not found']);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function device_tracking(Request $request)
    {
        $data = $request->all();
        $success = $updated = $not_found = 0;
        $response = [];
        $message = '';
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            if (!empty($data)) {
                Common::addLog($data);
                foreach ($data as $k => $val) {
                    if (!empty($val)) {
                        $check_connected = DeviceTracking::select('id')->where(['uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']])->first();
                        if (empty($check_connected->id)) {
                            $device_info = ProductStock::where(['uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']])->first();
                            if (!empty($device_info->uuid)) {
                                $check = DeviceTracking::insert(['mac_id' => $val['mac_id'] ?? NULL, 'user_id' => $user_details->id, 'lat' => $val['lat'], 'lan' => $val['lan'], 'uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']]);
                                if ($check) {
                                    $check_lost_device = DeviceRequest::select('user_id')->where(['uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']])->where('status', 0)->first();
                                    if (!empty($check_lost_device->user_id)) {
                                        $tracking_user = User::where(['id' => $check_lost_device->user_id])->first();
                                        if (!empty($tracking_user->id)) {
                                            $msg = "Your device found";
                                            $payload = ['lat' => $val['lat'], 'lan' => $val['lan']];
                                            $this->sendNotification($tracking_user->fcm_token, $msg, $payload);
                                        }
                                    }
                                    $success++;
                                }
                            } else {
                                $not_found++;
                            }
                        } else {

                            if(!empty($val['lat']) && !empty($val['lan'])){
                                $updated_data = ['lat' => $val['lat'], 'lan' => $val['lan'],'updated_at' => date('Y-m-d h:i:s')];
                            }else{
                                $updated_data = ['updated_at' => date('Y-m-d h:i:s')];
                            }

                            $update = DeviceTracking::where(['uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']])->update($updated_data);
                            if($update){
                                $updated++;
                            }

                            // $check_lost_device = DeviceRequest::select('user_id')->where(['uuid' => $val['uuid'], 'major' => $val['major'], 'minor' => $val['minor']])->where('status', 0)->where('user_id', "<>", $user_details->id)->first();
                            // if (!empty($check_lost_device->user_id)) {
                            //     $tracking_user = User::where(['id' => $check_lost_device->user_id])->first();
                            //     if (!empty($tracking_user->id)) {
                            //         $msg = "Your device found";
                            //         $payload = ['lat' => $val['lat'], 'lan' => $val['lan']];
                            //         //$this->sendNotification($tracking_user->fcm_token,$msg,$payload);
                            //     }
                            // }
                            
                        }
                    }
                }
            }

            if ($success > 0) {
                $response['status'] = true;
                $message = $success . ' Device successfully added, ';
            }

            if ($updated > 0) {
                $response['status'] = true;
                $message = $updated . ' Device successfully updated, ';
            }

            if ($not_found > 0) {
                $response['status'] = true;
                $message = $not_found . ' Device not found, ';
            }

            if (isset($response['status'])) {
                Common::addLog(['status' => 200, 'message' => $message . ' in Tracking' ?? "Success"]);
                return response()->json(['status' => 200, 'message' => $message . ' in Tracking' ?? "Success"], 200);
            } else {
                Common::addLog(['status' => 400, 'message' => 'Request Data not correct']);
                return response()->json(['status' => 400, 'message' => 'Request Data not correct'], 400);
            }

        } else {
            Common::addLog(['status' => 400, 'message' => 'User not found']);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function deviceLastLocation(Request $request)
    {
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;

        if(empty($uuid) || empty($major) || empty($minor)){
            Common::addLog(['status' => 400, 'message' => 'fields required']);
            return response()->json(['status' => 400, 'message' => 'fields required'], 400);
        }

        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'major' => 'required',
            'minor' => 'required',
        ]);
        if ($validator->fails()) {
            Common::addLog(['errors' => Helpers::error_processor($validator)]);
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $deviceTracking = DeviceTracking::select("lat", "lan", "updated_at")->where(['uuid' => $uuid, 'major' => $major, 'minor' => $minor])
                ->orderBy('updated_at', 'desc')
                ->first();
            if (!empty($deviceTracking->updated_at)) {
                $deviceTracking['date'] = strtotime($deviceTracking->updated_at);
                unset($deviceTracking['updated_at']);
            }
            Common::addLog(['status' => 200, 'message' => 'success', 'data' => (!empty($deviceTracking) ? $deviceTracking : '')]);
            return response()->json(['status' => 200, 'message' => 'success', 'data' => $deviceTracking], 200);
        } else {
            Common::addLog(['status' => 400, 'message' => 'User not found']);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }
    public function request_device(Request $request)
    {
        $uuid = $request->uuid;
        $major = $request->major;
        $minor = $request->minor;
        $validator = Validator::make($request->all(), [
            'uuid' => 'required',
            'major' => 'required',
            'minor' => 'required',
        ]);

        if ($validator->fails()) {
            Common::addLog(['errors' => Helpers::error_processor($validator)]);
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $device_info = ProductStock::where(['uuid' => $uuid, 'major' => $major, 'minor' => $minor])->first();
            if (!empty($device_info->mac_id)) {
                $check_connected = DeviceRequest::select('id', 'status', 'last_updated')->where(['user_id' => $user_details->id, 'uuid' => $uuid, 'major' => $major, 'minor' => $minor])->first();
                if (!empty($check_connected->id)) {
                    if ($check_connected->status == 1) {
                        $check_connected->status = 0;
                    } else {
                        $check_connected->status = 1;
                    }
                    $check_connected->last_updated = date('Y-m-d h:i:s');
                    $check_connected->save();
                    Common::addLog(['status' => 200, 'request_status' => $check_connected->status, 'message' => 'Device request updated successfully']);
                    return response()->json(['status' => 200, 'request_status' => $check_connected->status, 'message' => 'Device request updated successfully'], 200);

                } else {
                    $check = DeviceRequest::insert(['user_id' => $user_details->id, 'uuid' => $uuid, 'major' => $major, 'minor' => $minor]);
                    if ($check) {
                        Common::addLog(['status' => 200, 'request_status' => 0, 'message' => 'Device request added successfully']);
                        return response()->json(['status' => 200, 'request_status' => 0, 'message' => 'Device request added successfully'], 200);
                    }
                }
            } else {
                Common::addLog(['status' => 400, 'message' => 'Device not found']);
                return response()->json(['status' => 400, 'message' => 'Device not found'], 400);
            }
        } else {
            Common::addLog(['status' => 400, 'message' => 'User not found']);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    //END DEVICE API's

    public function sendNotification($fcm_token, $msg, $payload)
    {
        $SERVER_ID = env('FIREBASE_NOTIF_SERVER_ID');
        $FCM_URL = env('FCM_URL');

        $registrationIds[] = $fcm_token; //$registration_id;
        $title = 'Keepr';
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
            'type' => 'device_found',
            "lat" => (string) $payload['lat'],
            "lan" => (string) $payload['lan']
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
        $res = json_decode($result, true);
        if (isset($res['success']) && $res['success'] == 1) {
            return true;
        } else {
            return false;
        }
        //echo "<pre>"; print_r($result); die;
    }

}
