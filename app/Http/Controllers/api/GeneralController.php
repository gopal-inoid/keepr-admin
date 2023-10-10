<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\EmailTemplates;
use App\Model\HelpTopic;
use App\CPU\Helpers;
use App\Model\ConnectedDevice;
use App\Model\Banner;
use App\Model\Product;
use App\Model\Order;
use App\Model\DeviceRequest;
use App\User;
use App\Common;
use Illuminate\Support\Facades\Http;
use App\Model\BusinessSetting;
use App\Model\ShippingMethod;
use App\Model\ShippingMethodRates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class GeneralController extends Controller
{
    public function get_pages(Request $request)
    { // page type = terms_condition, privacy_policy, support, about_us,faq
        if ($request->page_name) {
            if ($request->page_name == 'faq') {
                $data = HelpTopic::select('question', 'answer')->where('status', 1)->get();
            } else {
                $data = BusinessSetting::where('type', $request->page_name)->first();
            }
            if (!empty($data)) {
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $data], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'Page not available'], 200);
            }
        } else {
            return response()->json(['status' => 400, 'message' => 'Page not found'], 400);
        }
    }

    public function force_update(Request $request)
    {
        if (empty($request->platform)) {
            return response()->json(
                array(
                    'code' => 400,
                    'message' => 'platform is required'
                ),
                400
            );
        }
        $check = \DB::table('api_versions')->where('platform', $request->platform)->first();
        return response()->json(['code' => 200, 'data' => ['new_version' => $check->version ?? 1, 'force_update' => $check->status ?? 1], 'message' => 'need Force Update!'], 200);
    }

    public function get_banners()
    {
        $banners_list = Banner::where(['published' => 1])->get();
        if (!empty($banners_list)) {
            return response()->json(['status' => 200, 'message' => 'Success', 'data' => $banners_list], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'Banners not found'], 400);
        }
    }

    //START USER AUTH API's

    //GET MOBILE NO. CHECK AND VERIFY INTO DB AND SEND IN RESPONSE
    public function verify_user(Request $request)
    {
        $mobile = $request->mobile;
        $phone_code = $request->phone_code;
        $user = User::select('id', 'phone', 'is_active')->where(['phone_code' => $phone_code, 'phone' => $mobile])->first();
        if (!empty($user->id)) {
            if ($user->is_active != 1) {
                Common::addLog(['status' => 400, 'message' => 'Not Activated']);
                return response()->json(['status' => 400, 'message' => 'Not Activated'], 200);
            } else {
                Common::addLog(['status' => 200, 'message' => 'Success']);
                return response()->json(['status' => 200, 'message' => 'Success'], 200);
            }
        } else {
            Common::addLog(['status' => 200, 'message' => 'Success']);
            return response()->json(['status' => 200, 'message' => 'Success'], 200);
        }
    }

    //GET FIREBASE AUTH TOKEN. CHECK AND VERIFY THEN ADD INTO DB AND SEND USER INFO IN RESPONSE
    public function user_authentication(Request $request)
    {
        $token = $request->token;
        $fcm_token = $request->fcm_token;
        try {
            $auth = app('firebase.auth');
            //echo "<pre>"; print_r($auth); die;
            $verifiedIdToken = $auth->verifyIdToken($token);
        } catch (FailedToVerifyToken $e) {
            Common::addLog(['status' => 400, 'message' => $e->getMessage()]);
            return response()->json(['status' => 400, 'message' => $e->getMessage()], 400);
        }
        $auth_token = '';
        $uid = $verifiedIdToken->claims()->get('sub');
        $user = $auth->getUser($uid);

        if (!empty($user->phoneNumber)) {

            $mobile_number = preg_replace(
                '/\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/'
                ,
                '',
                $user->phoneNumber
            );

            preg_match(
                '/\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/'
                ,
                $user->phoneNumber,
                $mobile_code
            );

            $phone_code = $mobile_code[0] ?? '';
            $user_check = User::select('id', 'phone', 'firebase_auth_id', 'auth_access_token')->where(['phone' => $mobile_number])->first();
            if (empty($user_check->id)) {
                $get_user = User::create([
                    'phone' => $mobile_number,
                    'phone_code' => $phone_code,
                    'firebase_auth_id' => $uid,
                    'fcm_token' => $fcm_token
                ]);
                $auth_token = $this->auth_token($get_user->id, "");
            } else {
                //echo "<pre>"; print_r(); die;
                $auth_token = $this->auth_token($user_check->id, $user_check->auth_access_token, $fcm_token);
            }

            if ($auth_token != '') {
                Common::addLog(['status' => 200, 'phone' => $mobile_number, 'phone_code' => $phone_code, 'auth_token' => $auth_token, 'message' => 'Success']);
                return response()->json(['status' => 200, 'phone' => $mobile_number, 'phone_code' => $phone_code, 'auth_token' => $auth_token, 'message' => 'Success'], 200);
            } else {
                Common::addLog(['status' => 401, 'message' => 'Token not Authorized']);
                return response()->json(['status' => 401, 'message' => 'Token not Authorized'], 401);
            }

        } else {
            Common::addLog(['status' => 401, 'message' => 'Token not Authorized']);
            return response()->json(['status' => 401, 'message' => 'Token not Authorized'], 401);
        }

    }

    public function auth_token($id, $old_token = "", $fcm_token = "")
    {
        if ($old_token != "") {
            $token = $old_token;
        } else {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
            $token = $id . $token . $id;
        }

        $user = User::where('id', $id)->update(['auth_access_token' => $token, 'fcm_token' => $fcm_token]);

        if ($user) {
            return $token;
        } else {
            return false;
        }
    }

    public function logout(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user->id)) {
            User::where(['id' => $user->id])->update(['auth_access_token' => '']);
            return response()->json(['status' => 200, 'message' => 'Successfully Logout'], 200);
        } else {
            return response()->json(['status' => 200, 'message' => 'User not found'], 200);
        }
    }

    //END USER AUTH API's

    //START USER API's
    public function delete_user_account(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user->id)) {
            ConnectedDevice::where(['user_id' => $user->id])->delete();
            $deleted = $user->delete();
            if (!empty($deleted)) {
                return response()->json(['status' => 200, 'message' => 'User successfully deleted'], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'User not deleted'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function user_profile(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $all_data['phone'] = $user_details->phone;
            $get_orders = Order::select('payment_status', 'order_status', 'order_amount', 'shipping_address', 'billing_address')->where(['customer_id' => $user_details->id])->get();
            if (!empty($get_orders)) {
                $all_data['order_list'] = $get_orders;
            }
            Common::addLog([]);
            return response()->json(['status' => 200, 'message' => 'Success', 'data' => $all_data], 200);
        } else {
            Common::addLog([]);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function get_countries(Request $request)
    {
        $countries_list = \DB::table('country')->select(['id', 'name'])->get();
        $countries = [];
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            if (!empty($countries_list)) {
                foreach ($countries_list as $key => $country) {
                    $countries[$key] = $country;
                }
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $countries], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'User not found'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }
    public function get_states(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required'
        ], );

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $states = [];
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $states_list = \DB::table('states')->select(['id', 'name'])->where('country_id', $request->country_id)->get();

            $states = [];
            if (!empty($states_list)) {
                foreach ($states_list as $key => $state) {
                    $states[$key] = $state;
                }
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $states], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'User not found'], 400);
            }
        } else {
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function set_address(Request $request)
    {
        $type = $request->type;
        $validator = Validator::make($request->all(), [
            'phone' => 'required'
        ], [
            'phone.required' => 'Phone is required!'
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request->phone == '' || $request->phone == null) {
            return response()->json(['status' => 400, 'message' => "Phone can't be NULL"], 400);
        }

        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            if ($type == 'shipping') {
                $user_details->add_shipping_address = $request->address;
                $user_details->shipping_name = $request->name;
                $user_details->shipping_email = $request->email;
                $user_details->shipping_phone = $request->phone;
                $user_details->shipping_phone_code = $request->phone_code;
                $user_details->shipping_country = $request->country;
                $user_details->shipping_city = $request->city;
                $user_details->shipping_state = $request->state;
                $user_details->shipping_zip = $request->zip_code;
                $user_details->shipping_country_iso = $request->country_iso;
            } else {
                $user_details->street_address = $request->address;
                $user_details->name = $request->name;
                $user_details->email = $request->email;
                // $user_details->phone = $request->phone;
                // $user_details->phone_code = $request->phone_code;
                $user_details->billing_phone = $request->phone;
                $user_details->billing_phone_code = $request->phone_code;
                $user_details->country = $request->country;
                $user_details->city = $request->city;
                $user_details->state = $request->state;
                $user_details->zip = $request->zip_code;
                $user_details->country_iso = $request->country_iso;
            }

            $user_details->save();
            return response()->json(['status' => 200, 'message' => 'Address successfully updated'], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function get_address(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $shipping['address'] = $user_details->add_shipping_address;
            $shipping['name'] = $user_details->shipping_name;
            $shipping['email'] = $user_details->shipping_email;
            $shipping['phone'] = $user_details->shipping_phone;
            $shipping['phone_code'] = $user_details->shipping_phone_code;
            $shipping['country'] = $user_details->shipping_country;
            $shipping['country_name'] = $this->getCountryName($user_details->shipping_country);
            $shipping['city'] = $user_details->shipping_city;
            $shipping['state'] = $user_details->shipping_state;
            $shipping['state_name'] = $this->getStateName($user_details->shipping_state);
            $shipping['zip'] = $user_details->shipping_zip;
            $shipping['country_iso'] = $user_details->shipping_country_iso;
            $billing['address'] = $user_details->street_address;
            $billing['name'] = $user_details->name;
            $billing['email'] = $user_details->email;
            // $billing['phone_code'] = $user_details->phone_code;
            // $billing['phone'] = $user_details->phone;
            $billing['phone_code'] = $user_details->billing_phone_code;
            $billing['phone'] = $user_details->billing_phone;
            $billing['country'] = $user_details->country;
            $billing['country_name'] = $this->getCountryName($user_details->country);
            $billing['city'] = $user_details->city;
            $billing['state'] = $user_details->state;
            $billing['state_name'] = $this->getStateName($user_details->state);
            $billing['zip'] = $user_details->zip;
            $billing['country_iso'] = $user_details->country_iso;

            return response()->json(['status' => 200, 'message' => 'Success', 'shipping' => $shipping, 'billing' => $billing], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function order_history(Request $request)
    {
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $order_list = [];
            $get_orders = Order::select('id as order_id', 'order_status', 'expected_delivery_date', 'customer_id', 'mac_ids', 'order_amount', 'created_at')
                ->where(['customer_id' => $user_details->id])->orderBy('created_at','desc')->get();
            foreach ($get_orders as $k => $order) {
                $order_list[$k]['order_id'] = $order['order_id'];
                $order_list[$k]['customer_id'] = $order['customer_id'];
                $order_list[$k]['order_amount'] = number_format($order['order_amount'], 2);
                $order_list[$k]['order_date'] = date('F j,Y, h:i A', strtotime($order['created_at']));

                if (time() < strtotime($order['expected_delivery_date']) && ($order['order_status'] == 'processing' || $order['order_status'] == 'shipped')) {
                    $order_list[$k]['delivery_message'] = 'Estimated Delivery on ' . date('F j', strtotime($order['expected_delivery_date']));
                } elseif (time() > strtotime($order['expected_delivery_date']) && $order['order_status'] == 'delivered') {
                    $order_list[$k]['delivery_message'] = 'Delivered on ' . date('F j', strtotime($order['expected_delivery_date']));
                } else {
                    $order_list[$k]['delivery_message'] = "";
                }
                $mac_ids = [];
                if (!empty($order['mac_ids'])) {
                    $mac_ids = json_decode($order['mac_ids'], true);
                }
                $order_list[$k]['total_devices'] = count($mac_ids);
            }
            Common::addLog([]);
            return response()->json(['status' => 200, 'message' => 'Success', 'data' => $order_list], 200);
        } else {
            Common::addLog([]);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    public function order_tracking_detail(Request $request)
    {
        $tracking_pin = "7023210039414604"; //$request->pin;
        $tracking_link = $this->getShippingTrackingDetais($tracking_pin);
        Common::addLog([]);
        return response()->json(['status' => 200, 'message' => 'Success', 'data' => $tracking_link], 200);
    }

    public function order_detail(Request $request)
    {
        $order_id = $request->order_id;
        $auth_token = $request->headers->get('X-Access-Token');
        $user_details = User::where(['auth_access_token' => $auth_token])->first();
        if (!empty($user_details->id)) {
            $get_orders = Order::select('id', 'customer_id', 'mac_ids', 'payment_status', 'expected_delivery_date', 'order_status', 'order_amount', 'shipping_address', 'created_at', 'tracking_id')
                ->where(['id' => $order_id])->first();
            $total_mac_ids = [];
            if (!empty($get_orders->id)) {
                $get_orders->invoice_path = route('generate-invoice', [$get_orders->id]);
                $get_orders->amount = number_format($get_orders->order_amount, 2);
                unset($get_orders->order_amount);
                $get_orders->order_date = date('F j,Y, h:i A', strtotime($get_orders->created_at));

                $shipping_address = User::select('add_shipping_address', 'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_country', 'shipping_city', 'shipping_state', 'shipping_zip')
                    ->where(['id' => $get_orders->customer_id])->first();

                if (time() < strtotime($get_orders->expected_delivery_date) && ($get_orders->order_status == 'processing' || $get_orders->order_status == 'shipped')) {
                    $get_orders->delivery_message = 'Estimated Delivery on ' . date('F j', strtotime($get_orders->expected_delivery_date));
                } elseif (time() > strtotime($get_orders->expected_delivery_date) && $get_orders->order_status == 'delivered') {
                    $get_orders->delivery_message = 'Delivered on ' . date('F j', strtotime($get_orders->expected_delivery_date));
                } else {
                    $get_orders->delivery_message = "";
                }

                $tracking_pin = $get_orders->tracking_id;
                // $get_orders->tracking_summary = $this->getShippingTrackingSummary($tracking_pin);
                $get_orders->tracking_url = 'https://www.canadapost-postescanada.ca/track-reperage/en#/search?searchFor=' . $tracking_pin;
                $get_orders->shipping = [
                    'address' => $shipping_address->add_shipping_address ?? '',
                    'name' => $shipping_address->shipping_name ?? '',
                    'email' => $shipping_address->shipping_email ?? '',
                    'phone' => $shipping_address->shipping_phone ?? '',
                    'country' => $shipping_address->shipping_country ?? '',
                    'city' => $shipping_address->shipping_city ?? '',
                    'state' => $shipping_address->shipping_state ?? '',
                    'zip' => $shipping_address->shipping_zip ?? '',
                ];

                $product_ids = [];
                if (!empty($get_orders->mac_ids)) {
                    $mac_ids = json_decode($get_orders->mac_ids, true);
                    if (!empty($mac_ids)) {
                        foreach ($mac_ids as $k => $val) {
                            $total_mac_ids[$k] = $val;
                            if (!in_array($k, $product_ids)) {
                                array_push($product_ids, $k);
                            }
                        }

                        foreach ($product_ids as $k => $products) {
                            $product_d = Product::select('id', 'name', 'thumbnail', 'purchase_price')->where(['id' => $products])->first();
                            if (!empty($product_d->id)) {
                                //echo "<pre>"; print_r($total_mac_ids); die;
                                $product_d->price = number_format($product_d->purchase_price, 2);
                                $product_d->quantity = count($total_mac_ids[$product_d->id]['uuid'] ?? []);
                                $product_d->thumbnail = asset("/product/thumbnail/$product_d->thumbnail");
                                unset($product_d->purchase_price);
                                $product_data[] = $product_d;
                            }
                            $get_orders->order_items = $product_data ?? [];
                        }
                    }
                }

                //$get_orders->total_devices = count($mac_ids);
                Common::addLog([]);
                return response()->json(['status' => 200, 'message' => 'Success', 'data' => $get_orders], 200);
            } else {
                Common::addLog([]);
                return response()->json(['status' => 400, 'message' => 'Order not found'], 400);
            }
        } else {
            Common::addLog([]);
            return response()->json(['status' => 400, 'message' => 'User not found'], 400);
        }
    }

    //END USER API's

    public function sendNotification(Request $request)
    {
        $SERVER_ID = env('FIREBASE_NOTIF_SERVER_ID');
        $FCM_URL = env('FCM_URL');

        $token = $request->token; //"f_4tUDB2Q0qcaoCeCN0L4T:APA91bGTWw5jIg4aQXy8jharK3CaXAjj6qukWe7t2r3vf8Uao2oCasPTVY1hnrEGH_a78cCmSlQgzt1m1_T3JoilpE8BtcqOJjb58XBHbXY8crhEfm5AsO9okao-dl2bTHuwPCfRmNlq";
        $title = 'Keepr App';
        $registrationIds[] = $token; //$registration_id;
        if ($request->type == 'order_placed') {
            $data1 = [
                'title' => $title,
                'message' => "This is Keepr Test Message",
                'vibrate' => 1,
                'sound' => 1,
                'type' => 'order_placed',
                'order_id' => 10
            ];
        } elseif ($request->type == 'device_found') {
            $data1 = [
                'title' => $title,
                'message' => "This is Keepr Test Message",
                'vibrate' => 1,
                'sound' => 1,
                'type' => 'device_found',
                "lat" => "2323.23",
                "lan" => "34413.32"
            ];
        }

        // prep the bundle
        $notification = [
            'title' => $title,
            'body' => "This is Keepr Test Message",
            'vibrate' => '1',
            'sound' => 'default',
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

        echo "<pre>";
        print_r($result);
        die;

        //return $result;
    }

    public function send_test_email(Request $request)
    {
        $userData = $this->getDataforEmail($order_id = null);
        if (!empty($userData)) {
            $userdata = User::find($userData['customer_id']);
            $userData['username'] = $userdata['name'] ?? "Keepr User";
            $userData['email'] = $userdata->email ?? "";
            $test = $this->sendKeeprEmail('order-confirmed-customer', $userData);
            if (isset($test) && $test == true) {
                return response()->json(['status' => 200, 'message' => 'Mail sent successfully'], 200);
            } elseif (isset($test) && $test == 2) {
                return response()->json(['status' => 400, 'message' => 'Something went wrong.'], 200);
            } else {
                return response()->json(['status' => 400, 'message' => 'failed'], 200);
            }
        }
    }

    public function get_shipping_rates(Request $request)
    {

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (strpos($authHeader, 'Basic ') === 0) {
                $base64Credentials = substr($authHeader, 6);
                $credentials = base64_decode($base64Credentials);
                list($username, $password) = explode(':', $credentials);
            }
        }

        $xmlInput = file_get_contents('php://input');
        if (!empty($xmlInput)) {
            $xml = simplexml_load_string($xmlInput);
            if ($xml !== false) {
                // You can now access the elements of the XML like this:
                $mailedBy = (string) $xml->{'customer-number'};
                $weight = (float) $xml->{'parcel-characteristics'}->weight;
                $originPostalCode = (string) $xml->{'origin-postal-code'};

                // $postalCode = (string) $xml->destination->domestic->{'postal-code'};
                $length = (int) $xml->{'parcel-characteristics'}->dimensions->length;
                $width = (int) $xml->{'parcel-characteristics'}->dimensions->width;
                $height = (int) $xml->{'parcel-characteristics'}->dimensions->height;
                if (isset($xml->destination->domestic)) {
                    $postalCode = (string) $xml->destination->domestic->{'postal-code'} . " Canada";
                } elseif (isset($xml->destination->{'united-states'})) {
                    $postalCode = (string) $xml->destination->{'united-states'}->{'zip-code'} . " United-States";
                } elseif (isset($xml->destination->international)) {
                    $postalCode = (string) $xml->destination->international->{'country-code'} . " International";
                }
            }
        }

        $result = $this->getShippingRates($username, $password, $mailedBy, $originPostalCode, $postalCode, $weight, $length, $width, $height);
        if (isset($result)) {
            return response()->json(['status' => 200, 'message' => 'Response recieved successfully', 'data' => $result], 200);
        } else {
            return response()->json(['status' => 400, 'message' => 'failed'], 200);
        }
    }
}