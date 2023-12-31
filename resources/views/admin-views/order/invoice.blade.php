<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\CPU\translate('invoice') }}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <table style="table-layout: fixed; width: 100%;">
        <tbody>
            <tr>
                <td class="" width="30%">
                    <span class="text-end float-left">
                        {{-- $company_web_logo -- }}
                        {{-- <img height="80" src="{{asset("/public/company/$company_web_logo")}}" alt=""> --}}
                        <img height="80" style="float-left" src="{{ asset('/public/company/Keepr-logo-black.png') }}"
                            alt="">
                    </span>
                </td>
                <td width="40%"></td>
                <td width="30%">
                    <table class="tbl-padded" style="text-align: right;">
                        <caption style="text-transform: uppercase; text-align: right; font-size: 30pt;">
                            <strong>
                                Invoice
                            </strong>
                        </caption>

                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div>
        <hr>
        <table style="table-layout: fixed; width: 100%;">
            <tbody>
                <tr>
                    <td width="40%">
                        <div class="col-sm-12">
                            <strong>
                                {{ \App\CPU\translate('date') }}:
                                {{ date('d-m-Y h:i:s a', strtotime($order['created_at'])) }}
                            </strong>
                        </div>
                    </td>
                    <td width="30%"></td>
                    <td width="30%">
                        <div class="col-sm-6 text-sm-end">
                            <strong>
                                {{ \App\CPU\translate('invoice') }} No
                            </strong>
                            #{{ $order->id }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
    </div>
    <div>
        <table style="table-layout: fixed; width: 100%;">

            <tbody>
                <tr>
                    <td width="30%">
                        <div class="col-sm-6 text-sm-end order-sm-1">
                            @if (!empty($shippingInfo))
                                <span class="h3" style="font-size:16px; font-weight:bold;">{{ \App\CPU\translate('shipping_to') }} </span>
                                <div class="h4 montserrat-normal-600">

                                    <p> {{ !empty($shippingInfo['name']) ? $shippingInfo['name'] : 'Name not found' }}
                                    </p>
                                    <p> {{ !empty($shippingInfo['email']) ? $shippingInfo['email'] : 'Email not found' }}
                                    </p>
                                    <p> {{ !empty($shippingInfo['phone_code']) ? $shippingInfo['phone_code'] : 'Phonecode not found' }}
                                        &nbsp;
                                        {{ !empty($shippingInfo['phone']) ? $shippingInfo['phone'] : 'Phone not found' }}
                                    </p>
                                    <p> {{ !empty($shippingInfo['address']) ? $shippingInfo['address'] : 'Address not found' }}
                                    </p>
                                    <p> {{ !empty($shippingInfo['city']) ? $shippingInfo['city'] : 'City not found' }}
                                    <p> {{ !empty($shippingInfo['state']) ? $shippingInfo['state'] : 'State not found' }}
                                        {{ !empty($shippingInfo['zip']) ? $shippingInfo['zip'] : 'Zip not found' }}
                                    </p>
                                    <p> {{ !empty($shippingInfo['country']) ? $shippingInfo['country'] : 'Country not found' }}
                                    </p>
                                </div>
                            @else
                                <span class="h3" style="font-size:16px; font-weight:bold;">{{ \App\CPU\translate('customer_info') }} </span>
                                <div class="h4 montserrat-normal-600">
                                    <p>{{ $order->customer != null ? $order->customer['name'] : \App\CPU\translate('name_not_found') }}
                                    </p>
                                    @if (isset($order->customer) && $order->customer['id'] != 0)
                                        <p>{{ $order->customer != null ? $order->customer['email'] : \App\CPU\translate('email_not_found') }}
                                        </p>
                                        <p>{{ $order->customer != null ? $order->customer['phone'] : \App\CPU\translate('phone_not_found') }}
                                        </p>
                                        <p>{{ $order->customer != null ? $order->customer['country'] : \App\CPU\translate('country_not_found') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </td>
                    <td width="40%">
                    </td>
                    <td width="30%">
                        <div class="col-sm-6 order-sm-0">
                            @if (!empty($order->customer->street_address))
                                <span class="h3" style="font-size:16px; font-weight:bold;">{{ \App\CPU\translate('billing_address') }} </span>
                                <div class="h4 montserrat-normal-600">
                                    <p>{{ !empty($billingInfo['name']) ? $billingInfo['name'] : 'Name not found' }}</p>
                                    <p>{{ !empty($billingInfo['email']) ? $billingInfo['email'] : 'Email not found' }}
                                    </p>
                                    <p>{{ !empty($billingInfo['phone_code']) ? $billingInfo['phone_code'] : 'PhoneCode not found' }}
                                        &nbsp;{{ !empty($billingInfo['phone']) ? $billingInfo['phone'] : 'Phone not found' }}
                                    </p>
                                    <p>{{ !empty($billingInfo['address']) ? $billingInfo['address'] : 'Address not found' }}
                                    </p>
                                    <p>{{ !empty($billingInfo['city']) ? $billingInfo['city'] : 'City not found' }}
                                    <p>{{ !empty($billingInfo['state']) ? $billingInfo['state'] : 'State not found' }}
                                        {{ !empty($billingInfo['zip']) ? $billingInfo['zip'] : 'Zip not found' }}</p>
                                    <p>{{ !empty($billingInfo['country']) ? $billingInfo['country'] : 'Country not found' }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div>
        <table style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th align="left" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            {{ \App\CPU\translate('SL') }}
                        </strong>
                    </th>
                    <th align="center" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            {{ \App\CPU\translate('Product Name') }}
                        </strong>
                    </th>
                    <th align="center" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            {{ \App\CPU\translate('Device Info') }}
                        </strong>
                    </th>
                    <th align="right" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            Price
                        </strong>
                    </th>
                    <th align="right" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            Qty.
                        </strong>
                    </th>
                    <th align="right" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            Total Amount
                        </strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php($i = 0)
                @php($grand_total_qty = $grand_total_amt = 0)
                @if (!empty($products))
                    @foreach ($products as $key => $detail)
                        @php($i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td style="text-align:center;">
                                <div class="media align-items-center gap-10">
                                    {{-- <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$detail['thumbnail']}}" width="50px;" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'" class="avatar avatar-60 rounded" alt=""> --}}
                                    <div>
                                        <a href="#" class="title-color hover-c1"
                                            style="color:black; text-decoration:none !important; ">
                                            <h3>{{ substr($detail['name'], 0, 50) }}{{ strlen($detail['name']) > 50 ? '...' : '' }}
                                            </h3>
                                        </a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if (!empty($detail['mac_ids']))
                                    @foreach ($detail['mac_ids'] as $k => $val)
                                        <strong>UUID: </strong>{{ $val['uuid'] }}<br />
                                        <strong>Major: </strong>{{ $val['major'] }}<br />
                                        <strong>Minor: </strong>{{ $val['minor'] }}<br />
                                    @endforeach
                                @endif
                            </td>
                            <td style="text-align:center;">
                                @php($total_price = 0)
                                @if (!empty($detail['mac_ids']))
                                    @foreach ($detail['mac_ids'] as $val)
                                        @php($total_price += $detail['price'])
                                        US ${{ $detail['price'] ?? '' }}<br /><br /><br /><br />
                                    @endforeach
                                @endif
                            </td>
                            <td style="text-align:center;">{{ count($detail['mac_ids']) }}</td>
                            <td style="text-align:center;">US ${{ number_format($total_price, 2) }}</td>
                            @php($grand_total_qty += count($detail['mac_ids']))
                            @php($grand_total_amt += $total_price)
                        </tr>
                    @endforeach
                @else
                    @if (!empty($order->product_info))
                        @php($product_info = json_decode($order->product_info, true));
                        @if (!empty($product_info))
                            @foreach ($product_info as $k => $val)
                                @php($i++)
                                <tr>
                                    <td style="text-align:center;">{{ $i }}</td>
                                    <td style="text-align:center;">
                                        <div class="media align-items-center gap-10">
                                            {{-- <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$val['thumbnail']}}" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'" class="avatar avatar-60 rounded" alt=""> --}}
                                            <div> <a href="#" class="title-color hover-c1"
                                                    style="text-decoration:none !important; color:black !important;">
                                                    <h3>{{ substr($val['product_name'], 0, 30) }}{{ strlen($val['product_name']) > 10 ? '...' : '' }}
                                                    </h3>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align:center;">--</td>
                                    <td style="text-align:right;">
                                        @php($total_price = 0)
                                        @if (!empty($order->per_device_amount))
                                            @php($perdevice_amount = json_decode($order->per_device_amount, true))
                                            @if (!empty($perdevice_amount))
                                                @php($total_price += $perdevice_amount[$k] ?? 0)
                                                US ${{ $perdevice_amount[$k] ?? 0 }}
                                            @endif
                                        @endif
                                    </td>
                                    <td style="text-align:right;">{{ $val['order_qty'] ?? 0 }}</td>
                                    <td style="text-align:right;">US ${{ number_format($total_price, 2) }}</td>
                                    @php($grand_total_qty += $val['order_qty'] ?? 0)
                                    @php($grand_total_amt += $total_price)
                                </tr>
                            @endforeach
                        @endif
                    @endif
                @endif
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><strong>{{ $grand_total_qty }}</strong></td>
                    <td style="text-align:right;"><strong>US ${{ number_format($grand_total_amt, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1cm; padding-bottom: 1cm;">
        @php($shipping = $order['shipping_cost'])
        <table style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="text-align:left;">{{ \App\CPU\translate('Other info') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <label><strong>{{ \App\CPU\translate('Tax info') }}</strong>: </label>
                        @php($tx_amt = $ship_amt = 0)
                        @foreach ($tax_info as $product_id => $taxes)
                            @php($tx_amt = $taxes['amount'])
                            <strong>{{ $taxes['title'] }}</strong><br />
                        @endforeach
                    </td>
                    <td style="text-align:right;"><strong>US ${{ number_format($tx_amt, 2) }}</strong></td>
                </tr>
                <tr>
                    <td>
                        <label><strong>{{ \App\CPU\translate('Shipping info') }}</strong>:
                        </label><br />
                        <strong>Shipping Co.:
                            {{ json_decode($order->shipping_rates, true)[0]['service_name'] ?? '' }}</strong><br />
                        <strong>Duration:
                            {{ json_decode($order->shipping_rates, true)[0]['delivery_days'] ?? '' }}
                            Days</strong><br />
                        <strong>Service Code:
                            {{ json_decode($order->shipping_rates, true)[0]['service_code'] ?? '' }}</strong>
                    </td>
                    <td style="text-align:right;">
                        <strong>US
                            ${{ number_format(json_decode($order->shipping_rates, true)[0]['shipping_rate'] ?? 0, 2) }}</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <hr />
                        <h4><strong>Grand Total</strong></h4>
                    </td>
                    <td style="text-align:right;">
                        <hr />
                        <h4><strong>US ${{ number_format($total_order_amount, 2) }}</strong></h4>
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="border-top: 1px solid #eee;">
            @if ($order->order_note)
                Order Note: {{ $order->order_note }}
            @endif
        </div>
    </div>


</body>

</html>
