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
use App\User;
use App\Model\Product;
use App\Model\Admin;
use App\Model\ShippingMethod;
use App\Model\ShippingMethodRates;
use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Mail\Message;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        try {
            Helpers::currency_load();
        } catch (\Exception $exception) {

        }
    }

    public function CheckDeviceExists($type, $value)
    {
        $check = ProductStock::where($type, $value)->count();
        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function print_r($str)
    {
        echo "<pre>";
        print_r($str);
        die;
    }

    public function getAdminDetail($field = null)
    {
        if ($field != null) {
            //return Admin::select($field)->first()->$field ?? "";
            return Helpers::get_business_settings($field);
        } elseif ($field != null && is_array($field)) {
            return "";
        } else {
            return "";
        }
    }

    public function getOrderAttr($mac_ids)
    {
        $attr = [];
        $total_orders = 0;
        if (!empty($mac_ids)) {
            $macids = json_decode($mac_ids, true);
            if (!empty($macids)) {
                foreach ($macids as $k => $val) {
                    $total_orders += count($val['uuid']);
                    $product_name = $this->getProductAttr($k, 'name');
                    $attr['product_name'][] = $product_name ?? "";
                    if (!empty($val)) {
                        foreach ($val['uuid'] as $k1 => $val1) {
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

    public function getOrderProductAttr($product_info)
    {
        $attr = [];
        if (!empty($product_info)) {
            $product_info = json_decode($product_info, true);
            if (!empty($product_info)) {
                foreach ($product_info as $k => $val) {
                    $attr['product_name'][] = $val['product_name'] ?? "";
                    $attr['total_orders'][] = $val['order_qty'] ?? 0;
                }
                return $attr;
            }
        }

        return [];
    }

    public function getProductAttr($product_id, $type)
    {
        $products_attr = Product::select($type)->where('id', $product_id)->first();
        return $products_attr->$type ?? "";
    }
    public function getCountryName($id)
    {
        $country_names = \DB::table('country')->select('name')->where('id', $id)->first();
        return $country_names->name ?? "";
    }

    public function getStateName($id)
    {
        $state_names = \DB::table('states')->select('name')->where('id', $id)->first();
        return $state_names->name ?? "";
    }

    public function save_invoice($id)
    {
        $company_phone = BusinessSetting::where('type', 'company_phone')->first()->value;
        $company_email = BusinessSetting::where('type', 'company_email')->first()->value;
        $company_name = BusinessSetting::where('type', 'company_name')->first()->value;
        $company_web_logo = BusinessSetting::where('type', 'company_web_logo')->first()->value;
        $order = Order::where('id', $id)->first();
        $data["email"] = $order->customer != null ? $order->customer["email"] : \App\CPU\translate('email_not_found');
        $data["client_name"] = $order->customer != null ? $order->customer["f_name"] . ' ' . $order->customer["l_name"] : \App\CPU\translate('customer_not_found');
        $data["order"] = $order;
        $products = [];
        $tax_info = [];
        $shipping_info = [];
        $total_orders = 0;
        $total_order_amount = $order->order_amount ?? 0;
        if (!empty($order->mac_ids)) { // stocks
            $mac_ids = json_decode($order->mac_ids, true);
            if (!empty($mac_ids)) {
                foreach ($mac_ids as $k => $val) {
                    $total_orders += count($mac_ids[$k]['uuid']);
                    $prod = Product::select('name', 'thumbnail', 'purchase_price')->find($k);
                    $products[$k]['name'] = $prod->name ?? "";
                    $products[$k]['thumbnail'] = $prod->thumbnail ?? "";
                    if (!empty($order->per_device_amount)) {
                        $perdevice_amount = json_decode($order->per_device_amount, true);
                        if (!empty($perdevice_amount)) {
                            $products[$k]['price'] = $perdevice_amount[$k] ?? 0;
                        } else {
                            $products[$k]['price'] = $prod->purchase_price ?? 0;
                        }
                    } else {
                        $products[$k]['price'] = $prod->purchase_price ?? 0;
                    }
                    if (!empty($val)) {
                        foreach ($val['uuid'] as $k1 => $val1) {
                            $products[$k]['mac_ids'][$k1]['uuid'] = $val1;
                            $products[$k]['mac_ids'][$k1]['major'] = $val['major'][$k1];
                            $products[$k]['mac_ids'][$k1]['minor'] = $val['minor'][$k1];
                        }
                    }
                }
            }
        }

        if (!empty($order->taxes)) {
            $taxes = json_decode($order->taxes, true);
            if (!empty($taxes)) {
                $tax_info = $taxes;
            }
        }

        if (!empty($order->shipping_method_id) && !empty($order->shipping_mode)) {
            $shipping = ShippingMethod::where(['id' => $order->shipping_method_id])->first();
            $shipping_method_rates = ShippingMethodRates::select('normal_rate', 'express_rate')->where('shipping_id', $order->shipping_method_id)->where('country_code', $this->getCountryName($order->customer->country))->first();
            $shipping_info['title'] = $shipping->title ?? "";
            if ($order->shipping_mode == 'normal_rate') {
                $shipping_info['duration'] = $shipping->normal_duration ?? "";
                $shipping_info['mode'] = 'Regular Rate';
                $shipping_info['amount'] = $shipping_method_rates->normal_rate ?? 0;
            } elseif ($order->shipping_mode == 'express_rate') {
                $shipping_info['duration'] = $shipping->express_duration ?? "";
                $shipping_info['mode'] = 'Express Rate';
                $shipping_info['amount'] = $shipping_method_rates->express_rate ?? 0;
            }
        }

        $mpdf_view = View::make(
            'admin-views.order.invoice',
            compact('order', 'company_phone', 'total_orders', 'products', 'company_name', 'company_email', 'company_web_logo', 'total_order_amount', 'shipping_info', 'tax_info')
        );
        Helpers::save_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function sendKeeprEmail($template_type, $user_data, $attachment = null)
    {
        $emailServices_smtp = Helpers::get_business_settings('mail_config');
        $files = null;
        if ($attachment != null) {
            $files = [$attachment];
        }

        if ($emailServices_smtp['status'] == 1) {
            try {

                $email_temp = EmailTemplates::where(['name' => $template_type])->where('status', 1)->first();
                if (!empty($email_temp->id)) {
                    $email_temp->subject = str_replace("{STATUS}", $user_data['order_status'] ?? "", $email_temp->subject);
                    $email_temp->body = str_replace("{STATUS}", $user_data['order_status'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{USERNAME}", $user_data['username'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_ID}", $user_data['order_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{PRODUCT_NAME}", $user_data['product_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{DEVICE_UUID}", $user_data['device_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{QTY}", $user_data['qty'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{EMAIL}", $user_data['email'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TOTAL_PRICE}", $user_data['total_price'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_DATE}", $user_data['order_date'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ORDER_NOTE}", $user_data['order_note'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_NAME}", $user_data['billing_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_EMAIL}", $user_data['billing_email'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_ADDRESS}", $user_data['billing_address'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_CITY}", $user_data['billing_city'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_STATE}", $user_data['billing_state'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_COUNTRY}", $user_data['billing_country'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{BILLING_ZIP}", $user_data['billing_zip'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_NAME}", $user_data['shipping_name'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_EMAIL}", $user_data['shipping_email'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_ADDRESS}", $user_data['shipping_address'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_CITY}", $user_data['shipping_city'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_STATE}", $user_data['shipping_state'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_COUNTRY}", $user_data['shipping_country'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_ZIP}", $user_data['shipping_zip'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPMENT_INFORMATION}", $user_data['shipment_information'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{ESTIMATED_DELIVERY_DATE}", $user_data['estimated_delivery_date'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TRACKING_ID}", $user_data['tracking_id'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{COMPANY_NAME}", 'Keepr', $email_temp->body);

                    $email_temp->body = str_replace("{TOTAL_AMOUNT}", $user_data['total_price'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TOTAL_QTY}", $user_data['qty'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TAX_AMOUNT}", $user_data['tax_amount'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{PRICE}", $user_data['price'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{GRAND_TOTAL}", $user_data['grand_total_price'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{GRAND_TOTAL_QTY}", $user_data['grand_total_qty'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_INFO}", $user_data['shipping_info'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_TITLE}", $user_data['shipping_title'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_DURATION}", $user_data['shipping_duration'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_MODE}", $user_data['shipping_mode'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{SHIPPING_AMOUNT}", $user_data['shipping_amount'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{TAX_INFO}", $user_data['tax_info'] ?? "", $email_temp->body);
                    $email_temp->body = str_replace("{COMPANY_LOGO}", '<img src="' . url('/public/public/company/Keepe_logo.png') . '" />', $email_temp->body);

                    $data['email'] = $user_data['email'] ?? "";
                    $data['subject'] = $email_temp->subject ?? "";
                    $data["body"] = $email_temp->body ?? "";

                    if (!empty($data['email'])) {
                        Mail::send('email-templates.mail-tester', $data, function ($message) use ($data, $files) {
                            $message->to($data["email"])
                                ->subject($data["subject"]);
                            if ($files != null) {
                                foreach ($files as $file) {
                                    $message->attach($file);
                                }
                            }
                        });
                    }
                }
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }
            if (isset($error)) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    public function replacedEmailVariables($status, $body, $userData = null)
    {
        if ($userData != null) {
            $notif_keys = [
                "{STATUS}",
                "{USERNAME}",
                "{ORDER_ID}",
                "{PRODUCT_NAME}",
                "{DEVICE_UUID}",
                "{QTY}",
                "{TOTAL_PRICE}",
                "{COMPANY_NAME}",
                "{COMPANY_LOGO}",
                "{ORDER_DATE}",
                "{ORDER_NOTE}",
                "{BILLING_NAME}",
                "{BILLING_EMAIL}",
                "{BILLING_ADDRESS}",
                "{SHIPPING_NAME}",
                "{SHIPPING_EMAIL}",
                "{SHIPPING_ADDRESS}",
                "{SHIPMENT_INFORMATION}",
                "{ESTIMATED_DELIVERY_DATE}",
                "{TRACKING_ID}"
            ];

            $notif_values = [
                $status,
                $userData['username'],
                $userData['order_id'],
                $userData['product_name'],
                $userData['device_id'],
                $userData['qty'],
                $userData['total_price'],
                $userData['company_name'],
                $userData['company_logo']
            ];
        } else {
            $notif_keys = ["{STATUS}"];
            $notif_values = [$status];
        }
        $body = str_replace($notif_keys, $notif_values, $body);
        return $body;
    }

    public function getTaxCalculation($amount, $country_name, $state_name)
    {
        $taxes = \DB::table('tax_calculation')->select('tax_amt', 'type')->where('country', $country_name)->first();
        $tax_calculation = [];
        if (!empty($taxes)) {
            $tax_rates = json_decode($taxes->tax_amt, true);
            if ($taxes->type == "fixed") {
                if ($tax_rates[0]['tax1'] != "") {
                    $tax_amt = (($amount * $tax_rates[0]['tax1']) / 100);
                    $tax_calculation[0]['title'] = $tax_rates[0]['tax1'] . "% " . $tax_rates[0]['tax_txt1'];
                    $tax_calculation[0]['amount'] = number_format($tax_amt, 2);
                    $tax_calculation[0]['percent'] = $tax_rates[0]['tax1'];
                } else {
                    return [];
                }
            } else {
                //echo "<pre>"; print_r($tax_rates); die;
                foreach ($tax_rates as $taxval) {
                    if ($state_name == $taxval['state']) {
                        $tax_amt1 = (($amount * $taxval['tax1']) / 100);
                        $tax_calculation[0]['title'] = $taxval['tax1'] . "% " . $taxval['tax_txt1'];
                        $tax_calculation[0]['amount'] = number_format($tax_amt1, 2);
                        $tax_calculation[0]['percent'] = number_format($taxval['tax1'], 3);

                        if (!empty($taxval['tax2'])) {
                            $tax_amt2 = (($amount * $taxval['tax2']) / 100);
                            $tax_calculation[1]['title'] = $taxval['tax2'] . "% " . $taxval['tax_txt2'];
                            $tax_calculation[1]['amount'] = number_format($tax_amt2, 2);
                            $tax_calculation[1]['percent'] = number_format($taxval['tax2'], 3);
                        }
                    }
                }
            }

            return $tax_calculation;

        } else {
            return [];
        }
    }

    public function getDataforEmail($order_id)
    {

        $update_order = Order::where(['id' => $order_id])->first();
        if (!empty($update_order->id)) {
            // $order_attribute = $this->getOrderProductAttr($update_order->product_info ?? "");
            // if (!empty($order_attribute['product_name']) && is_array($order_attribute['product_name'])) {
            //     $product_names = implode(',', $order_attribute['product_name']);
            // }
            // if (!empty($order_attribute['total_orders']) && is_array($order_attribute['total_orders'])) {
            //     $product_qty = implode(',', $order_attribute['total_orders']);
            // }


            $productArray = array();
            if (!empty($update_order) && !empty($update_order->product_info)) {
                $productArray['product_id'] = array_key_first(json_decode($update_order->product_info, true));
                $productArray['price'] = array_values(json_decode($update_order->per_device_amount, true))[0];

                $info = json_decode($update_order->product_info, true);
                foreach ($info as $k => $val) {
                    $productArray['product_name'] = $val['product_name'];
                    $productArray['product_qty'] = $val['order_qty'] ?? 0;
                }
                $productArray['total_price'] = $productArray['product_qty'] * $productArray['price'];

            }

            $shippingrates = json_decode($update_order->shipping_rates, true);
            if (!empty($shippingrates)) {
                foreach ($shippingrates as $k => $val) {
                    $productArray['shipping_amount'] = $val['shipping_rate'];
                    $productArray['shipping_title'] = $val['service_name'];
                    $productArray['shipping_mode'] = $val['service_code'];
                    $productArray['shipping_duration'] = $val['delivery_days'] . " Days";
                    $productArray['shipping_info'] = $val['service_name'] . " " . $val['service_code'] . " US $" . $val['shipping_rate'] . " " . $val['delivery_days'] . " Days";
                }
            }

            $product_id = $productArray['product_id'] ?? ""; //array_keys(json_decode($update_order['mac_ids'], true))[0] ?? 0;
            $product_qty_info = $productArray['product_qty'] ?? ""; //json_decode($update_order->mac_ids, true);
            $price_info = $productArray['price'] ?? ""; //json_decode($update_order->per_device_amount, true);
            $product_name = $productArray['product_name'] ?? ""; //$product_info[$product_id]['product_name'] ?? "";
            $product_qty = $productArray['product_qty']; //!empty($product_qty_info[$product_id]['uuid']) ? count($product_qty_info[$product_id]['uuid']) : 0;
            $price = $productArray['price'] ?? ""; // $price_info[$product_id] ?? 0;
            $total_price = $productArray['total_price'] ?? ""; //$price * $product_qty;

            // $shipping_info = $productArray['shipping_rates'];
            // if (!empty($update_order->shipping_method_id) && !empty($update_order->shipping_mode)) {
            //     $shipping = ShippingMethod::where(['id' => $update_order->shipping_method_id])->first();
            //     $shipping_method_rates = ShippingMethodRates::select('normal_rate', 'express_rate')->where('shipping_id', $update_order->shipping_method_id)->where('country_code', $this->getCountryName($update_order->customer->country))->first();
            //     $shipping_info['title'] = $shipping->title ?? "";
            //     if ($update_order->shipping_mode == 'normal_rate') {
            //         $shipping_info['duration'] = $shipping->normal_duration ?? "";
            //         $shipping_info['mode'] = 'Regular Rate';
            //         $shipping_info['amount'] = $shipping_method_rates->normal_rate ?? 0;
            //     } elseif ($update_order->shipping_mode == 'express_rate') {
            //         $shipping_info['duration'] = $shipping->express_duration ?? "";
            //         $shipping_info['mode'] = 'Express Rate';
            //         $shipping_info['amount'] = $shipping_method_rates->express_rate ?? 0;
            //     }
            // }
            $taxData = json_decode($update_order->taxes, true);

            $userData['order_id'] = $update_order->id;
            $userData['customer_id'] = $update_order->customer_id;
            $userData['order_status'] = $update_order->order_status;
            $userData['product_name'] = $product_name ?? "";
            $userData['qty'] = $product_qty ?? "";
            $userData['grand_total_qty'] = $product_qty ?? "";
            $userData['total_price'] = number_format($total_price);
            $userData['price'] = $price;
            $userData['shipping_title'] = $productArray['shipping_title'] ?? "";
            $userData['duration'] = $productArray['shipping_duration'] ?? "";
            $userData['mode'] = $productArray['shipping_mode'] ?? "";
            $userData['shipping_amount'] = $productArray['shipping_amount'] ?? ""; //$shipping_info['amount'] ?? "";
            $userData['grand_total_price'] = number_format($update_order->order_amount, 2);
            $userData['shipping_info'] = $productArray['shipping_info'] ?? ""; //$shipping_info['title'] ?? "" . " " . $shipping_info['duration'] ?? "" . " " . $shipping_info['mode'] ?? "";
            $userData['shipping_title'] = $productArray['shipping_title'] ?? ""; //$shipping_info['title'] ?? "";
            $userData['shipping_duration'] = $productArray['shipping_duration'] ?? ""; //$shipping_info['duration'] ?? "";
            $userData['shipping_mode'] = $productArray['shipping_mode'] ?? ""; //$shipping_info['mode'] ?? "";
            $userData['tax_info'] = count($taxData) == 0 ? "" : $taxData[0]['title'] ?? "" . " " . $taxData[0]['percent'] ?? "";
            $userData['tax_amount'] = count($taxData) == 0 ? "" : $taxData[0]['amount'] ?? "";
            return $userData;
        } else {
            return false;
        }
    }


    function getShippingRates($origin_postal_code, $postal_code, $country_code, $_weight, $length, $width, $height)
    {
        $username = env('CANADAPOST_USERANME');
        $password = env('CANADAPOST_PASSWORD');
        $token = base64_encode($username . ":" . $password);
        $len = $length;
        $wid_th = $width;
        $hei_ght = $height;
        $mailedBy = "9941977";
        // REST URL
        $service_url = env('CANADAPOST_URL') . '/rs/ship/price';

        // Create GetRates request xml
        $originPostalCode = $origin_postal_code;
        // $postalCode = $postal_code;
        // $pCode = explode(" ", $postal_code);
        $weight = $_weight;

        //echo "<pre>"; print_r($pCode); die;

        $xmlRequest = <<<XML
        <mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v4">
           <customer-number>{$mailedBy}</customer-number> 
              <!--<quote-type>counter</quote-type>-->
            <parcel-characteristics>
                <dimensions>
                    <length>{$len}</length>
                    <width>{$wid_th}</width>
                    <height>{$hei_ght}</height>
                </dimensions>
                <weight>{$weight}</weight>
            </parcel-characteristics>
            <origin-postal-code>{$originPostalCode}</origin-postal-code>
            <destination>
        XML;

        if (strpos($country_code, 'CA') !== false) {
            // Postal code indicates Canada
            $xmlRequest .= <<<XML
                <domestic>
                    <postal-code>{$postal_code}</postal-code>
                </domestic>
            XML;
        } elseif (strpos($country_code, 'US') !== false) {
            // Postal code indicates United States
            $xmlRequest .= <<<XML
                <united-states>
                    <zip-code>{$postal_code}</zip-code>
                </united-states>
            XML;
        } else {
            // Postal code indicates International
            $xmlRequest .= <<<XML
                <international>
                    <country-code>{$country_code}</country-code>
                    <postal-code>{$postal_code}</postal-code>
                </international>
            XML;
        }

        $xmlRequest .= <<<XML
            </destination>
        </mailing-scenario>
        XML;

        // echo "<pre>"; print_r($xmlRequest); die;

        $curl = curl_init($service_url); // Create REST Request
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Basic ' . $token,
                'Content-Type: application/vnd.cpc.ship.rate-v4+xml',
                'Accept: application/vnd.cpc.ship.rate-v4+xml'
            )
        );
        $curl_response = curl_exec($curl); // Execute REST Request
        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl) . "\n";
        }
        curl_close($curl);
        $xml = simplexml_load_string($curl_response);
        $jsonArray = json_decode(json_encode($xml), true);
        $isTrackingArray = array("DOM.RP", "DOM.EP", "DOM.XP", "DOM.XP.CERT", "DOM.PC", "USA.EP", "USA.TP", "USA.TP.LVM", "USA.XP", "INT.XP", "INT.TP");
        $deliveryDaysForNoneTracking = array(
            'USA.SP.AIR' => '5-10',
            'INT.IP.AIR' => '6-12',
            'INT.IP.SURF' => '4-12',
            'INT.SP.AIR' => '6-12',
            'INT.SP.SURF' => '4-12',
            'SAUDI.REG' => '5-10',
            'SAUDI.EXP' => '3-5',
        );
        $finalArray = array();
        if (!empty($jsonArray)) {
            if (!empty($jsonArray['price-quote'][0]['service-code'])) {
                foreach ($jsonArray as $k => $val) {
                    if (!empty($val) && is_array($val)) {
                        foreach ($val as $j => $child) {
                            $deliveryDays = array_key_exists($child['service-code'], $deliveryDaysForNoneTracking) ? $deliveryDaysForNoneTracking[$child['service-code']] : "";
                            $del_Days = in_array($child['service-code'], $isTrackingArray) ? $child['service-standard']['expected-transit-time'] ?? "" : $deliveryDays;
                            $array = array();
                            $array['service_name'] = !empty($child['service-name']) ? $child['service-name'] . " - via Canada Post" : "";
                            $array['service_code'] = $child['service-code'] ?? "";
                            $array['is_tracking'] = in_array($child['service-code'], $isTrackingArray) ? "1" : "0";
                            $array['shipping_rate'] = round((($child['price-details']['base'] * 0.74) + 1), 2);
                            $array['expected_delivery_date'] = $child['service-standard']['expected-delivery-date'] ?? "";
                            $array['is_guanranteed'] = $child['service-standard']['guaranteed-delivery'] == "true" ? '1' : '0';
                            $array['delivery_days'] = $del_Days;
                            if ($child['service-code'] == 'INT.IP.SURF' || $child['service-code'] == 'INT.SP.SURF') {
                                if($array['is_guanranteed'] == '1'){
                                    $array['delivery_txt'] = 'Guanranteed delivery on ' . $array['expected_delivery_date'];
                                }else{
                                    $array['delivery_txt'] = 'Estimated delivery date: ' . $del_Days . " Business weeks";
                                }
                            } else {
                                if($array['is_guanranteed'] == '1'){
                                    $array['delivery_txt'] = 'Guanranteed delivery on ' . $array['expected_delivery_date'];
                                }else{
                                    $array['delivery_txt'] = 'Estimated delivery on ' . $del_Days . " Business days";
                                }
                            }
                            array_push($finalArray, $array);
                        }
                    }
                }
            }
        }
        usort($finalArray, function ($a, $b) {
            if ($a == $b)
                return 0;
            return ($a['shipping_rate'] < $b['shipping_rate']) ? -1 : 1;
        });

        return $finalArray;
    }

    function getShippingTrackingSummary($pin)
    {
        $username = env('CANADAPOST_USERANME');
        $password = env('CANADAPOST_PASSWORD');
        $token = base64_encode($username . ":" . $password);
        // REST URL
        $service_url = env('CANADAPOST_URL') . '/vis/track/pin/' . $pin . '/summary';
        $curl = curl_init($service_url); // Create REST Request
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array(
                'Authorization: Basic ' . $token,
                'Accept: application/vnd.cpc.track-v2+xml'
            )
        );
        $curl_response = curl_exec($curl); // Execute REST Request
        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl) . "\n";
        }
        curl_close($curl);
        $xml = simplexml_load_string($curl_response);
        $jsonArray = json_decode(json_encode($xml), true);

        echo "<pre>";
        print_r($jsonArray);
        die;

        $finalArray = array();
        // foreach ($jsonArray as $k => $val) {
        //     if (!empty($val) && is_array($val)) {
        //         foreach ($val as $j => $child) {
        //             $array = array();
        //             $array['service_name'] = $child['service-name'] ?? 0;
        //             // $array['mode'] = strtolower(str_replace(' ', '_', $child['service-name'] ?? ''));
        //             $array['service_code'] = $child['service-code'] ?? 0;
        //             $array['shipping_rate'] = (($child['price-details']['due'] * 0.74) + 1);
        //             $array['expected_delivery_date'] = $child['service-standard']['expected-delivery-date'];
        //             $array['is_guanranteed'] = $child['service-standard']['guaranteed-delivery'] == true ? '1' : '0';
        //             // $array['tracking'] = $child['price-details']['options']['option']['option-code'] == 'DC' ? '1' : '0';
        //             $array['delivery_days'] = $child['service-standard']['expected-transit-time'];
        //             array_push($finalArray, $array);
        //         }
        //     }
        // }
        return $finalArray;
    }

    function getShippingTrackingDetais($pin)
    {
        $trackingUrl = 'https://www.canadapost-postescanada.ca/track-reperage/en#/search?searchFor=' . $pin;
        return $trackingUrl;
        exit;


        //     $username = env('CANADAPOST_USERANME');
        //     $password = env('CANADAPOST_PASSWORD');
        //     $token = base64_encode($username . ":" . $password);
        //     // echo $token;exit;
        //     // REST URL
        //     $service_url = env('CANADAPOST_URL') . '/vis/track/pin/' . $pin . '/detail';
        //     $curl = curl_init($service_url); // Create REST Request
        //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt(
        //         $curl,
        //         CURLOPT_HTTPHEADER,
        //         array(
        //             'Authorization: Basic ' . $token,
        //             'Accept: application/vnd.cpc.track-v2+xml'
        //         )
        //     );
        //     $curl_response = curl_exec($curl); // Execute REST Request
        //     if (curl_errno($curl)) {
        //         echo 'Curl error: ' . curl_error($curl) . "\n";
        //     }
        //     curl_close($curl);
        //     $xml = simplexml_load_string($curl_response);
        //     $jsonArray = json_decode(json_encode($xml), true);

        //     echo "<pre>";
        //     print_r($jsonArray);
        //     die;

        //     $finalArray = array();
        //     // foreach ($jsonArray as $k => $val) {
        //     //     if (!empty($val) && is_array($val)) {
        //     //         foreach ($val as $j => $child) {
        //     //             $array = array();
        //     //             $array['service_name'] = $child['service-name'] ?? 0;
        //     //             // $array['mode'] = strtolower(str_replace(' ', '_', $child['service-name'] ?? ''));
        //     //             $array['service_code'] = $child['service-code'] ?? 0;
        //     //             $array['shipping_rate'] = (($child['price-details']['due'] * 0.74) + 1);
        //     //             $array['expected_delivery_date'] = $child['service-standard']['expected-delivery-date'];
        //     //             $array['is_guanranteed'] = $child['service-standard']['guaranteed-delivery'] == true ? '1' : '0';
        //     //             // $array['tracking'] = $child['price-details']['options']['option']['option-code'] == 'DC' ? '1' : '0';
        //     //             $array['delivery_days'] = $child['service-standard']['expected-transit-time'];
        //     //             array_push($finalArray, $array);
        //     //         }
        //     //     }
        //     // }
        //     return $finalArray;
    }


    // function getShippingServiceDetails($customer_number, $origin_postal_code, $postal_code, $_weight, $length, $width, $height, $service_code)
    // {
    //     $username = env('CANADAPOST_USERANME');
    //     $password = env('CANADAPOST_PASSWORD');
    //     $token = base64_encode($username . ":" . $password);
    //     $mailedBy = $customer_number;
    //     $len = $length;
    //     $wid_th = $width;
    //     $hei_ght = $height;
    //     $serviceCode = $service_code;

    //     // REST URL
    //     $service_url = env('CANADAPOST_URL') . '/rs/ship/price';

    //     // Create GetRates request xml
    //     $originPostalCode = $origin_postal_code;
    //     $postalCode = $postal_code;
    //     $pCode = explode(" ", $postal_code);
    //     $weight = $_weight;

    //     //echo "<pre>"; print_r($pCode); die;

    //     $xmlRequest = <<<XML
    //     <mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v4">
    //         <customer-number>{$mailedBy}</customer-number>
    //         <parcel-characteristics>
    //             <dimensions>
    //                 <length>{$len}</length>
    //                 <width>{$wid_th}</width>
    //                 <height>{$hei_ght}</height>
    //             </dimensions>
    //             <weight>{$weight}</weight>
    //         </parcel-characteristics>
    //         <origin-postal-code>{$originPostalCode}</origin-postal-code>
    //         <destination>
    //     XML;

    //     if (strpos($postalCode, 'Canada') !== false) {
    //         // Postal code indicates Canada
    //         $xmlRequest .= <<<XML
    //             <domestic>
    //                 <postal-code>{$pCode[0]}</postal-code>
    //             </domestic>
    //         XML;
    //     } elseif (strpos($postalCode, 'United-States') !== false) {
    //         // Postal code indicates United States
    //         $xmlRequest .= <<<XML
    //             <united-states>
    //                 <zip-code>{$pCode[0]}</zip-code>
    //             </united-states>
    //         XML;
    //     } elseif (strpos($postalCode, 'International') !== false) {
    //         // Postal code indicates International
    //         $xmlRequest .= <<<XML
    //             <international>
    //                 <country-code>{$pCode[0]}</country-code>
    //             </international>
    //         XML;
    //     }

    //     $xmlRequest .= <<<XML
    //         </destination>
    //     </mailing-scenario>
    //     XML;

    //     //echo "<pre>"; print_r($xmlRequest); die;

    //     $curl = curl_init($service_url); // Create REST Request
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
    //     curl_setopt($curl, CURLOPT_POST, true);
    //     curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
    //     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt(
    //         $curl,
    //         CURLOPT_HTTPHEADER,
    //         array(
    //             'Authorization: Basic ' . $token,
    //             'Content-Type: application/vnd.cpc.ship.rate-v4+xml',
    //             'Accept: application/vnd.cpc.ship.rate-v4+xml'
    //         )
    //     );
    //     $curl_response = curl_exec($curl); // Execute REST Request
    //     if (curl_errno($curl)) {
    //         echo 'Curl error: ' . curl_error($curl) . "\n";
    //     }
    //     curl_close($curl);
    //     $xml = simplexml_load_string($curl_response);
    //     $jsonArray = json_decode(json_encode($xml), true);

    //     //echo "<pre>"; print_r($jsonArray); die;

    //     $finalArray = array();
    //     foreach ($jsonArray as $k => $val) {
    //         if (!empty($val) && is_array($val)) {
    //             foreach ($val as $j => $child) {
    //                 $array = array();
    //                 $array['text'] = $child['service-name'] ?? 0;
    //                 $array['mode'] = strtolower(str_replace(' ', '_', $child['service-name'] ?? ''));
    //                 $array['service_code'] = $child['service-code'] ?? 0;
    //                 $array['shipping_rate'] = $child['price-details']['due'];
    //                 $array['expected_delivery_date'] = $child['service-standard']['expected-delivery-date'];
    //                 $array['is_guanranteed'] = $child['service-standard']['guaranteed-delivery'] == true ? '1' : '0';
    //                 // $array['tracking'] = $child['price-details']['options']['option']['option-code'] == 'DC' ? '1' : '0';
    //                 $array['delivery_days'] = $child['service-standard']['expected-transit-time'];
    //                 array_push($finalArray, $array);
    //             }
    //         }
    //     }
    //     foreach ($finalArray as $k => $val) {
    //         if ($val['service_code'] == $serviceCode) {
    //             return $val;
    //         }
    //     }
    // }

    function noncontractshipment()
    {
        $username = env('CANADAPOST_DEV_USERANME');
        $password = env('CANADAPOST_DEV_PASSWORD');
        $mailedBy = env('CANADAPOST_CUSTOMER_NUMBER');
        // REST URL
        $service_url = 'https://ct.soa-gw.canadapost.ca/rs/' . $mailedBy . '/ncshipment';

        // Create CreateShipment request xml
        // $requestedShippingPoint = 'H2B1A0';

        $xmlRequest = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<non-contract-shipment xmlns="http://www.canadapost.ca/ws/ncshipment-v4">
	<requested-shipping-point>H2B1A0</requested-shipping-point>
	<delivery-spec>
		<service-code>DOM.EP</service-code>
		<sender>
			<company>Capsule Corp.</company>
			<contact-phone>1 (613) 450-5345</contact-phone>
			<address-details>
				<address-line-1>502 MAIN ST N</address-line-1>
				<city>MONTREAL</city>
				<prov-state>QC</prov-state>
				<postal-zip-code>H2B1A0</postal-zip-code>
			</address-details>
		</sender>
		<destination>
			<name>John Doe</name>
			<company>ACME Corp</company>
			<address-details>
				<address-line-1>123 Postal Drive</address-line-1>
				<city>Ottawa</city>
				<prov-state>ON</prov-state>
				<country-code>CA</country-code>
				<postal-zip-code>K1P5Z9</postal-zip-code>
			</address-details>
		</destination>
		<options>
			<option>
				<option-code>DC</option-code>
			</option>
		</options>
		<parcel-characteristics>
			<weight>15</weight>
			<dimensions>
				<length>1</length>
				<width>1</width>
				<height>1</height>
			</dimensions>
		</parcel-characteristics>
		<preferences>
			<show-packing-instructions>true</show-packing-instructions>
		</preferences>
		<references>
			<cost-centre>ccent</cost-centre>
			<customer-ref-1>custref1</customer-ref-1>
			<customer-ref-2>custref2</customer-ref-2>
	    </references>
	</delivery-spec>
</non-contract-shipment>
XML;

        $curl = curl_init($service_url); // Create REST Request
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.ncshipment-v4+xml', 'Accept: application/vnd.cpc.ncshipment-v4+xml'));
        $curl_response = curl_exec($curl); // Execute REST Request
        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl) . "\n";
        }
        curl_close($curl);
        $xml = simplexml_load_string($curl_response);
        $jsonArray = json_decode(json_encode($xml), true);
        return $jsonArray;
    }
}
?>