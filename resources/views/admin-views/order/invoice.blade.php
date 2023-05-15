<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{\App\CPU\translate('invoice')}}</title>
    <meta http-equiv="Content-Type" content="text/html;" />
    <meta charset="UTF-8">
    <style media="all">
        * {
            margin: 0;
            padding: 0;
            line-height: 1.3;
            font-family: sans-serif;
            color: #333542;
        }


        /* IE 6 */
        * html .footer {
            position: absolute;
            top: expression((0-(footer.offsetHeight)+(document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body.clientHeight)+(ignoreMe=document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop))+'px');
        }

        body {
            font-size: .75rem;
        }

        img {
            max-width: 100%;
        }

        .customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        table {
            width: 100%;
        }

        table thead th {
            padding: 8px;
            font-size: 11px;
            text-align: left;
        }

        table tbody th,
        table tbody td {
            padding: 8px;
            font-size: 11px;
        }

        table.fz-12 thead th {
            font-size: 12px;
        }

        table.fz-12 tbody th,
        table.fz-12 tbody td {
            font-size: 12px;
        }

        table.customers thead th {
            background-color: #0177CD;
            color: #fff;
            font-size: 14px;
        }

        table.customers tbody th,
        table.customers tbody td {
            background-color: #FAFCFF;
        }

        table.calc-table th {
            text-align: left;
        }

        table.calc-table td {
            text-align: right;
        }

        table.calc-table td.text-left {
            text-align: left;
        }

        .table-total {
            font-family: Arial, Helvetica, sans-serif;
        }


        .text-left {
            text-align: left !important;
        }

        .pb-2 {
            padding-bottom: 8px !important;
        }

        .pb-3 {
            padding-bottom: 16px !important;
        }

        .text-right {
            text-align: right;
        }

        .content-position {
            padding: 0px 15px;
        }

        .content-position-y {
            padding: 0px 40px;
        }

        .text-white {
            color: white !important;
        }

        .bs-0 {
            border-spacing: 0;
        }

        .text-center {
            text-align: center;
        }

        .mb-1 {
            margin-bottom: 4px !important;
        }

        .mb-2 {
            margin-bottom: 8px !important;
        }

        .mb-4 {
            margin-bottom: 24px !important;
        }

        .mb-30 {
            margin-bottom: 30px !important;
        }

        .px-10 {
            padding-left: 10px;
            padding-right: 10px;
        }

        .fz-14 {
            font-size: 14px;
        }

        .fz-12 {
            font-size: 12px;
        }

        .fz-10 {
            font-size: 10px;
        }

        .font-normal {
            font-weight: 400;
        }

        .border-dashed-top {
            border-top: 1px dashed #ddd;
        }

        .font-weight-bold {
            font-weight: 700;
        }

        .bg-light {
            background-color: #F7F7F7;
        }

        .py-30 {
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .py-4 {
            padding-top: 24px;
            padding-bottom: 24px;
        }

        .d-flex {
            display: flex;
        }

        .gap-2 {
            gap: 8px;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }

        .align-items-center {
            align-items: center;
        }

        .justify-content-center {
            justify-content: center;
        }

        a {
            color: rgba(0, 128, 245, 1);
        }

        .p-1 {
            padding: 4px !important;
        }

        .h2 {
            font-size: 1.5em;
            margin-block-start: 0.83em;
            margin-block-end: 0.83em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }

        .h4 {
            margin-block-start: 1.33em;
            margin-block-end: 1.33em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            font-weight: bold;
        }

        .avatar {
            position: relative;
            display: inline-block;
            width: 2.625rem;
            height: 2.625rem;
            border-radius: 0.3125rem;
        }

        .avatar-60 {
            width: 60px !important;
            min-width: 60px !important;
            height: 60px !important;
        }

        .title-color {
            color: var(--title-color);
        }

        a {
            color: #377dff;
            text-decoration: none;
            background-color: transparent;
        }

        #keepr_invooice_table {
            border-bottom: 1px solid #535b61;
        }

        #Invoice_date_keepr {
            width: 50%;
        }

        #invoice_heading {
            font-size: 1.75rem !important;
        }

        .date_invoice {
            font-weight: 600;
            font-size: 17px;
        }

        .invoice_no {
            font-weight: 600;
            font-size: 17px;
        }

        .Total_amount_invoice {
            font-weight: 600;
            font-size: 17px;
        }
    </style>
</head>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>
    <div class="first">
        <table class="content-position mb-30" id="keepr_invooice_table">
            <tr>
                <th id="Invoice_date_keepr" style="text-align: left;">
                    {{-- <img height="50" src="{{asset("/public/company/$company_web_logo")}}" alt=""> --}}
                    <img height="80" src="{{asset("/public/company/Keepr-logo-black.png")}}" alt="" style="margin-left : 10px">
                </th>
                <th class="content-position-y text-right" id="Invoice_date_keepr">
                    <h4 class="text-uppercase mb-1 fz-14" id="invoice_heading">
                        <!-- {{\App\CPU\translate('invoice')}} #{{ $order->id }} -->
                        Invoice
                    </h4>
                </th>
            </tr>

        </table>

        <table class="bs-0 mb-30 px-10">
            <tr>
                <th class="content-position-y text-left" id="Invoice_date_keepr">
                    <h4 class="fz-14"><strong class="date_invoice">{{\App\CPU\translate('date')}} </strong>: {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}</h4>
                </th>
                <th class="content-position-y text-right" id="Invoice_date_keepr">
                    <h4 class="text-uppercase mb-1 fz-14">
                        <strong class="invoice_no">{{\App\CPU\translate('invoice')}}</strong> #{{ $order->id }}
                    </h4>
                </th>
            </tr>
        </table>
    </div>
    <div class="">
        <section>
            <table class="content-position-y fz-12">
                <tr>
                    <td class="font-weight-bold p-1">
                        <table>
                            <tr>
                                <td id="Invoice_date_keepr">
                                    @if (!empty($order->customer->add_shipping_address))
                                    <span class="h2" style="margin: 0px;">{{\App\CPU\translate('shipping_to')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer ? $order->customer['add_shipping_address'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer ? $order->customer['shipping_city'] : ""}} {{$order->customer ? $order->customer['shipping_zip'] : ""}}</p>
                                    </div>
                                    @else
                                    <span class="h2" style="margin: 0px;">{{\App\CPU\translate('customer_info')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>
                                        @if (isset($order->customer) && $order->customer['id']!=0)
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
                                        @endif
                                    </div>
                                    @endif
                                    </p>
                                </td>
                                <td id="Invoice_date_keepr"></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td class="text-right">
                                    @if (!empty($order->customer->street_address))
                                    <span class="h2">{{\App\CPU\translate('billing_address')}} </span>
                                    <div class="h4 montserrat-normal-600">
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer['name'] ? $order->customer['name'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer['phone'] ? $order->customer['phone'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer['street_address'] ? $order->customer['street_address'] : ""}}</p>
                                        <p style=" margin-top: 6px; margin-bottom:0px;">{{$order->customer['city'] ? $order->customer['city'] : ""}} {{$order->customer['zip'] ? $order->customer['zip'] : ""}}</p>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </section>
    </div>

    <br>

    <div class="">
        <div class="content-position-y">
            <table class="customers bs-0">
                <thead>
                    <tr>
                        <th>{{\App\CPU\translate('SL')}}</th>
                        <th>{{\App\CPU\translate('Product Image')}}</th>
                        <th>{{\App\CPU\translate('Product Name')}}</th>
                        <th>MAC ID</th>
                        <th>Total Device</th>
                    </tr>
                </thead>
                <tbody>
                    @php($i=0)
                    @foreach($products as $key => $detail)
                    @php($i++)
                    <tr>
                        <td>{{$i}}</td>
                        <td>
                            <div class="media align-items-center gap-10">
                                <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$detail['thumbnail']}}" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'" class="avatar avatar-60 rounded" alt="">
                            </div>
                        </td>
                        <td>
                            <div>
                                <a href="#" class="title-color hover-c1">
                                    <h3>{{substr($detail['name'],0,30)}}{{strlen($detail['name'])>10?'...':''}}</h3>
                                </a>
                            </div>
                        </td>
                        <td>

                            @if(!empty($detail['mac_ids']))
                            @foreach($detail['mac_ids'] as $k => $val)
                            {{$val}}<br>
                            @endforeach
                            @endif

                        </td>
                        <td>
                            {{$total_orders}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @php($shipping=$order['shipping_cost'])
    <div class="content-position-y">
        <table class="fz-12">
            <tr>
                <th class="text-left">
                    <h4 class="fz-12 mb-1">{{\App\CPU\translate('payment_details')}}</h4>
                    <p class="fz-12 font-normal">
                        {{$order->payment_status}}
                        , {{date('y-m-d',strtotime($order['created_at']))}}
                    </p>
                </th>
                <th>
                    <table class="calc-table">
                        <tbody>
                            {{-- <tr>
                            <td class="p-1 text-left">{{\App\CPU\translate('sub_total')}}</td>
                            <td class="p-1">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($sub_total))}}</td>
            </tr>
            <tr>
                <td class="p-1 text-left">{{\App\CPU\translate('tax')}}</td>
                <td class="p-1">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($total_tax))}}</td>
            </tr>
            <tr>
                <td class="p-1 text-left">{{\App\CPU\translate('shipping')}}</td>
                <td class="p-1">{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($shipping))}}</td>
            </tr> --}}
            <tr>
                <td class="border-dashed-top font-weight-bold text-right"><b> <strong class="Total_amount_invoice">{{\App\CPU\translate('Total Amount')}} </strong>&nbsp; {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount))}}</b></td>
                <!-- <td class="border-dashed-top font-weight-bold">
                    {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount))}}
                </td> -->
            </tr>
            </tbody>
        </table>
        </th>
        </tr>
        </table>
    </div>
    <br>
    <br><br><br>

    <div class="row">
        <section>
            <table class="">
                <tr>
                    <th class="fz-12 font-normal pb-3">
                        {{\App\CPU\translate('If_you_require_any_assistance_or_have_feedback_or_suggestions_about_our_site,_you')}} <br /> {{\App\CPU\translate('can_email_us_at')}} <a href="mail::to({{ $company_email }})">{{ $company_email }}</a>
                    </th>
                </tr>
                <tr>
                    <th class="content-position-y bg-light py-4">
                        <div class="d-flex justify-content-center gap-2">
                            <div class="mb-2">
                                <i class="fa fa-phone"></i>
                                {{\App\CPU\translate('phone')}}
                                : {{ $company_phone }}
                            </div>
                            <div class="mb-2">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                                {{\App\CPU\translate('email')}}
                                : {{$company_email}}
                            </div>
                        </div>
                        <div class="mb-2">
                            {{url('/')}}
                        </div>
                        <div>
                            {{\App\CPU\translate('All_copy_right_reserved_©_'.date('Y').'_').$company_name}}
                        </div>
                    </th>
                </tr>
            </table>
        </section>
    </div>

</body>

</html>