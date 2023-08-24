<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{\App\CPU\translate('invoice')}}</title>
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
                    <span class="text-end">
                        {{-- $company_web_logo -- }}
                        {{-- <img height="80" src="{{asset("/public/company/$company_web_logo")}}" alt=""> --}}
                        <img height="80" src="{{asset("/public/company/Keepr-logo-black.png")}}" alt="">
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
    <div style="padding-top: 1cm; padding-bottom: 1cm;">
        <table style="table-layout: fixed; width: 100%;">
            <tbody>
                <tr>
                    <td width="40%">
                        <div class="col-sm-12">
                            <strong>
                                {{\App\CPU\translate('date')}}:  {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}
                            </strong>
                        </div>
                    </td>
                    <td width="30%"></td>
                    <td width="30%">
                        <div class="col-sm-6 text-sm-end">
                            <strong>
                                {{\App\CPU\translate('invoice')}} No
                            </strong>
                            #{{ $order->id }}
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1cm; padding-bottom: 1cm;">
        <table style="table-layout: fixed; width: 100%;">
            <tbody>
                <tr>
                    <td width="30%">
                        <div class="col-sm-6 text-sm-end order-sm-1">
                            @if (!empty($order->customer->add_shipping_address))
                            <span class="h2">{{\App\CPU\translate('shipping_to')}} </span>
                            <div class="h4 montserrat-normal-600">
                                <p>{{$order->customer !=null? $order->customer['shipping_name']:\App\CPU\translate('name_not_found')}}</p>
                                <p>{{$order->customer !=null? $order->customer['shipping_email']:\App\CPU\translate('email_not_found')}}</p>
                                <p>{{$order->customer !=null? $order->customer['shipping_phone']:\App\CPU\translate('phone_not_found')}}</p>
                                <p>{{$order->customer ? $order->customer['add_shipping_address'] : ""}}</p>
                                <p>{{$order->customer ? $order->customer['shipping_city'] : ""}} {{$order->customer ? $order->customer['shipping_zip'] : ""}}</p>
                            </div>
                            @else
                            <span class="h4">{{\App\CPU\translate('customer_info')}} </span>
                            <div class="h4 montserrat-normal-600">
                                <p>{{$order->customer !=null? $order->customer['name']:\App\CPU\translate('name_not_found')}}</p>
                                @if (isset($order->customer) && $order->customer['id']!=0)
                                <p>{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                                <p>{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
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
                            <span class="h2">{{\App\CPU\translate('billing_address')}} </span>
                            <div class="h4 montserrat-normal-600">
                                <p>{{$order->customer['name'] ? $order->customer['name'] : ""}}</p>
                                <p>{{$order->customer['billing_phone'] ? $order->customer['billing_phone'] : ""}}</p>
                                <p>{{$order->customer['street_address'] ? $order->customer['street_address'] : ""}}</p>
                                <p>{{$order->customer['city'] ? $order->customer['city'] : ""}} {{$order->customer['zip'] ? $order->customer['zip'] : ""}}</p>
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
                            {{\App\CPU\translate('SL')}}
                        </strong>
                    </th>
                    <th align="center" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            {{\App\CPU\translate('Product Name')}}
                        </strong>
                    </th>
                    <th align="center" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            {{\App\CPU\translate('Device Info')}}
                        </strong>
                    </th>
                    <th align="right" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            Price
                        </strong>
                    </th>
                    <th align="right" style="border-top: 1px solid #eee; padding: 5px;">
                        <strong>
                            Qty
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
                @php($i=0)
                @php($grand_total_qty = $grand_total_amt = 0)
                @foreach($products as $key => $detail)
                @php($i++)
                <tr>
                    <td>{{$i}}</td>
                    <td style="text-align:center;">
                        <div class="media align-items-center gap-10">
                            {{-- <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$detail['thumbnail']}}" width="50px;" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'" class="avatar avatar-60 rounded" alt=""> --}}
                            <div>
                                <a href="#" class="title-color hover-c1"><h6>{{substr($detail['name'],0,50)}}{{strlen($detail['name'])>50?'...':''}}</h6></a>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if(!empty($detail['mac_ids']))
                            @foreach($detail['mac_ids'] as $k => $val)
                                <strong>UUID: </strong>{{$val['uuid']}}<br />
                                <strong>Major: </strong>{{$val['major']}}<br />
                                <strong>Minor: </strong>{{$val['minor']}}<br />
                            @endforeach
                        @endif
                    </td>
                    <td style="text-align:center;">
                        @php($total_price = 0)
                        @if(!empty($detail['mac_ids']))
                            @foreach($detail['mac_ids'] as $val)
                                @php($total_price += $detail['price'])
                                ${{$detail['price'] ?? ''}}<br /><br /><br /><br />
                            @endforeach
                        @endif
                    </td>
                    <td style="text-align:center;">{{count($detail['mac_ids'])}}</td>
                    <td style="text-align:center;">${{number_format($total_price,2)}}</td>
                    @php($grand_total_qty += count($detail['mac_ids']))
                    @php($grand_total_amt += $total_price)
                </tr>
                @endforeach
                <tr>
                    <td><strong>Total</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:center;"><strong>{{$grand_total_qty}}</strong></td>
                    <td style="text-align:center;"><strong>${{number_format($grand_total_amt,2)}}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="padding-top: 1cm; padding-bottom: 1cm;">
        @php($shipping=$order['shipping_cost'])
        <table style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="text-align:left;">{{\App\CPU\translate('Other info')}}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <label><strong>{{\App\CPU\translate('Tax info')}}</strong>: </label>
                        @php($tx_amt = $ship_amt = 0)
                            @foreach($tax_info as $product_id => $taxes)
                                @php($tx_amt = $taxes['amount'])
                                <strong>{{$taxes['title']}}</strong><br />
                            @endforeach
                    </td>
                    <td style="text-align:center;"><strong>${{number_format($tx_amt,2)}}</strong></td>
                </tr>
                <tr>
                    <td>
                        <label><strong>{{\App\CPU\translate('Shipping info')}}</strong>: </label><br />
                        <strong>Shipping Co.: {{$shipping_info['title'] ?? ''}}</strong><br />
                        <strong>Duration: {{$shipping_info['duration'] ?? ''}}</strong><br />
                        <strong>Shipping Mode: {{$shipping_info['mode'] ?? ''}}</strong>
                    </td>
                    <td style="text-align:center;">
                        <strong>${{number_format($shipping_info['amount'] ?? 0,2)}}</strong>
                    </td>
                </tr>
                <tr>
                    <td><hr /><h4><strong>Grand Total</strong></h4></td>
                    <td style="text-align:center;"><hr />
                        <h4><strong>${{number_format($total_order_amount,2)}}</strong></h4>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="border-top: 1px solid #eee;"></div>
</body>
</html>