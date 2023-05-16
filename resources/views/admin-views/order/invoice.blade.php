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

        @import url('https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap');

        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal;
            font-weight: 400;
            font-display: block;
            src: url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.eot);
            src: url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.eot?#iefix) format("embedded-opentype"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.woff2) format("woff2"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.woff) format("woff"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.ttf) format("truetype"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-regular-400.svg#fontawesome) format("svg");
        }

        @font-face {
            font-family: "Font Awesome 5 Free";
            font-style: normal;
            font-weight: 900;
            font-display: block;
            src: url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.eot);
            src: url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.eot?#iefix) format("embedded-opentype"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.woff2) format("woff2"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.woff) format("woff"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.ttf) format("truetype"), url(https://harnishdesign.net/demo/html/koice/vendor/font-awesome/webfonts/fa-solid-900.svg#fontawesome) format("svg");
        }

        body {
            /* CSS Variables that may have been missed get put on body */
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Poppins", sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 22px;
            color: #535b61;
            background-color: #fff;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            background: #e7e9ed;
        }

        :root {
            --bs-font-sans-serif: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        }

        @media (prefers-reduced-motion: no-preference) {
            :root {
                scroll-behavior: smooth;
            }
        }

        .container-fluid {
            width: 100%;
            padding-right: var(--bs-gutter-x, .75rem);
            padding-left: var(--bs-gutter-x, .75rem);
            margin-right: auto;
            margin-left: auto;
        }

        .invoice-container {
            margin: 15px auto;
            padding: 70px;
            /* max-width: 850px; */
            background-color: #fff;
            border: 1px solid #ccc;
            -moz-border-radius: 6px;
            -webkit-border-radius: 6px;
            -o-border-radius: 6px;
            border-radius: 6px;
        }

        *,
        :after,
        :before {
            box-sizing: border-box;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }

        .text-center {
            text-align: center !important;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(var(--bs-gutter-y) * -1);
            margin-right: calc(var(--bs-gutter-x) * -.5);
            margin-left: calc(var(--bs-gutter-x) * -.5);
        }

        .align-items-center {
            align-items: center !important;
        }

        hr {
            margin: 1rem 0;
            color: inherit;
            background-color: currentColor;
            border: 0;
            opacity: 0.15;
        }

        hr:not([size]) {
            height: 1px;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
            line-height: 1.9;
        }

        .text-1 {
            font-size: 0.75rem !important;
        }

        .btn-group {
            position: relative;
            display: inline-flex;
            vertical-align: middle;
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * .5);
            padding-left: calc(var(--bs-gutter-x) * .5);
            margin-top: var(--bs-gutter-y);
        }

        @media (min-width: 576px) {
            .col-sm-7 {
                flex: 0 0 auto;
                width: 58.33333333%;
            }
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        @media (min-width: 576px) {
            .mb-sm-0 {
                margin-bottom: 0 !important;
            }

            .text-sm-start {
                text-align: left !important;
            }

            .col-sm-5 {
                flex: 0 0 auto;
                width: 41.66666667%;
            }

            .text-sm-end {
                text-align: right !important;
            }

            .col-sm-6 {
                flex: 0 0 auto;
                width: 50%;
            }

            .order-sm-1 {
                order: 1 !important;
            }

            .order-sm-0 {
                order: 0 !important;
            }
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1rem 1rem;
        }

        .p-0 {
            padding: 0 !important;
        }

        strong {
            font-weight: bolder;
        }

        a {
            color: #0071cc;
            text-decoration: none;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        .btn {
            display: inline-block;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: center;
            text-decoration: none;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
            background-color: transparent;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            border-radius: .25rem;
            transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .btn-light {
            color: #000;
            background-color: #f8f9fa;
            border-color: #f8f9fa;
        }

        .shadow-none {
            box-shadow: none !important;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .text-black-50 {
            color: rgba(0, 0, 0, .5) !important;
        }

        .btn-group-sm>.btn {
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
        }

        .btn-group>.btn {
            position: relative;
            flex: 1 1 auto;
        }

        .btn-group>.btn:not(:last-child):not(.dropdown-toggle) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        a:hover {
            color: #0a58ca;
        }

        a:hover,
        a:active {
            color: #005da8;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        .btn:hover {
            color: #212529;
        }

        .btn-light:hover {
            color: #000;
            background-color: #f9fafb;
            border-color: #f9fafb;
        }

        .btn-group>.btn:active,
        .btn-group>.btn:hover {
            z-index: 1;
        }

        .btn-group>.btn:not(:first-child) {
            margin-left: -1px;
        }

        .btn-group> :not(.btn-check)+.btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        img {
            vertical-align: inherit;
        }

        h4 {
            margin-top: 0;
            margin-bottom: .5rem;
            font-weight: 500;
            line-height: 1.2;
            font-size: calc(1.275rem + .3vw);
        }

        @media (min-width: 1200px) {
            h4 {
                font-size: 1.5rem;
            }
        }

        h4 {
            color: #0c2f54;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .text-7 {
            font-size: 1.75rem !important;
        }

        address {
            margin-bottom: 1rem;
            font-style: normal;
            line-height: inherit;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .fa {
            display: inline-block;
            -moz-osx-font-smoothing: grayscale;
            -webkit-font-smoothing: antialiased;
            font-style: normal;
            font-variant: normal;
            text-rendering: auto;
            line-height: 1;
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
        }

        .fa-print:before {
            content: "\f02f";
        }

        .fa-download:before {
            content: "\f019";
        }

        table {
            caption-side: bottom;
            border-collapse: collapse;
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            width: 100%;
            margin-bottom: 1rem;
            color: #535b61;
            vertical-align: top;
            border-color: #dee2e6;
        }

        thead {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        .card-header {
            padding: .5rem 1rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, .03);
            border-bottom: 1px solid rgba(0, 0, 0, .125);
            padding-top: .75rem;
            padding-bottom: .75rem;
        }

        .table>thead {
            vertical-align: bottom;
        }

        .card-header:first-child {
            border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        }

        tbody {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        .table>tbody {
            vertical-align: inherit;
        }

        tfoot {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        .card-footer {
            padding: .5rem 1rem;
            background-color: rgba(0, 0, 0, .03);
            border-top: 1px solid rgba(0, 0, 0, .125);
        }

        .card-footer:last-child {
            border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
        }

        tr {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        td {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        .col-3 {
            flex: 0 0 auto;
            width: 25%;
        }

        .table> :not(caption)>*>* {
            padding: .5rem .5rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }

        .table:not(.table-sm)> :not(caption)>*>* {
            padding: 0.75rem;
        }

        .table> :not(:last-child)> :last-child>* {
            border-bottom-color: inherit;
        }

        .col-4 {
            flex: 0 0 auto;
            width: 33.33333333%;
        }

        .col-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }

        .col-1 {
            flex: 0 0 auto;
            width: 8.33333333%;
        }

        .text-end {
            text-align: right !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }
    </style>
</head>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<body>



    <div class="container-fluid invoice-container snipcss-TSSAm">
        <header class="snipcss0-0-0-1">
            <div class="row align-items-center snipcss0-1-1-2">
                <div class="col-sm-7 text-center text-sm-start mb-3 mb-sm-0 snipcss0-2-2-3">
                    {{-- <img height="80" src="{{asset("/public/company/$company_web_logo")}}" alt=""> --}}
                    <img height="80" src="{{asset("/public/company/Keepr-logo-black.png")}}" alt="">
                </div>
                <div class="col-sm-5 text-center text-sm-end snipcss0-2-2-5">
                    <h4 class="text-7 mb-0 snipcss0-3-5-6">
                        Invoice
                    </h4>
                </div>
            </div>
            <hr class="snipcss0-1-1-7">
        </header>
        <main>
            <div class="row">
                <div class="col-sm-6">
                    <strong>
                        {{\App\CPU\translate('date')}}
                    </strong>
                    {{date('d-m-Y h:i:s a',strtotime($order['created_at']))}}
                </div>
                <div class="col-sm-6 text-sm-end">
                    <strong>
                        {{\App\CPU\translate('invoice')}}
                    </strong>
                    #{{ $order->id }}
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-6 text-sm-end order-sm-1">
                    @if (!empty($order->customer->add_shipping_address))
                    <span class="h2">{{\App\CPU\translate('shipping_to')}} </span>
                    <div class="h4 montserrat-normal-600">
                        <p>{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>
                        <p>{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                        <p>{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
                        <p>{{$order->customer ? $order->customer['add_shipping_address'] : ""}}</p>
                        <p>{{$order->customer ? $order->customer['shipping_city'] : ""}} {{$order->customer ? $order->customer['shipping_zip'] : ""}}</p>
                    </div>
                    @else
                    <span class="h4">{{\App\CPU\translate('customer_info')}} </span>
                    <div class="h4 montserrat-normal-600">
                        <p>{{$order->customer !=null? $order->customer['f_name'].' '.$order->customer['l_name']:\App\CPU\translate('name_not_found')}}</p>
                        @if (isset($order->customer) && $order->customer['id']!=0)
                        <p>{{$order->customer !=null? $order->customer['email']:\App\CPU\translate('email_not_found')}}</p>
                        <p>{{$order->customer !=null? $order->customer['phone']:\App\CPU\translate('phone_not_found')}}</p>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="col-sm-6 order-sm-0">
                    @if (!empty($order->customer->street_address))
                    <span class="h2">{{\App\CPU\translate('billing_address')}} </span>
                    <div class="h4 montserrat-normal-600">
                        <p>{{$order->customer['name'] ? $order->customer['name'] : ""}}</p>
                        <p>{{$order->customer['phone'] ? $order->customer['phone'] : ""}}</p>
                        <p>{{$order->customer['street_address'] ? $order->customer['street_address'] : ""}}</p>
                        <p>{{$order->customer['city'] ? $order->customer['city'] : ""}} {{$order->customer['zip'] ? $order->customer['zip'] : ""}}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="card-header">
                                <tr>
                                    <td class="col text-center">
                                        <strong>
                                            {{\App\CPU\translate('SL')}}
                                        </strong>
                                    </td>
                                    <td class="col text-center">
                                        <strong>
                                            {{\App\CPU\translate('Product Image')}}
                                        </strong>
                                    </td>
                                    <td class="col text-center">
                                        <strong>
                                            {{\App\CPU\translate('Product Name')}}
                                        </strong>
                                    </td>
                                    <td class="col text-center">
                                        <strong>
                                            MAC ID
                                        </strong>
                                    </td>
                                    <td class="col text-end">
                                        <strong>
                                            Total Device
                                        </strong>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                @php($i=0)
                                @foreach($products as $key => $detail)
                                @php($i++)
                                <tr>
                                    <td class="text-center">{{$i}}</td>
                                    <td class="text-center">
                                        <div class="media align-items-center gap-10">
                                            <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$detail['thumbnail']}}" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img2.jpg')}}'" class="avatar avatar-60 rounded" alt="">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <a href="#" class="title-color hover-c1">
                                                <h3>{{substr($detail['name'],0,30)}}{{strlen($detail['name'])>10?'...':''}}</h3>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center">

                                        @if(!empty($detail['mac_ids']))
                                        @foreach($detail['mac_ids'] as $k => $val)
                                        {{$val}}<br>
                                        @endforeach
                                        @endif

                                    </td>
                                    <td class="text-end">
                                        {{$total_orders}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            @php($shipping=$order['shipping_cost'])
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
                                        <td class="border-dashed-top font-weight-bold text-right"><b> <strong class="">{{\App\CPU\translate('Total Amount')}} </strong>&nbsp; {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount))}}</b></td>
                                    </tr>
                                    </tbody>
                                </table>
                                </th>
                                </tr>
                        </table>
                    </div>
                    </table>
                </div>
            </div>
            <br> <br> <br> <br>
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
    </div>
    </main>
    </div>





    <!-- 
    <div class="first">
        <table class="content-position mb-30" id="keepr_invooice_table">
            <tr>
                <th id="Invoice_date_keepr" style="text-align: left;">
                    {{-- <img height="50" src="{{asset("/public/company/$company_web_logo")}}" alt=""> --}}
                    <img height="80" src="{{asset("/public/company/Keepr-logo-black.png")}}" alt="" style="margin-left : 10px">
                </th>
                <th class="content-position-y text-right" id="Invoice_date_keepr">
                    <h4 class="text-uppercase mb-1 fz-14" id="invoice_heading">
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
    </div> -->
    <!-- <div class="">
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

    <br> -->

    <!-- <div class="">
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
    @php($shipping=$order['shipping_cost']) -->
    <!-- <div class="content-position-y">
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
            </tr>
            </tbody>
        </table>
        </th>
        </tr>
        </table>
    </div> -->


    <!-- <div class="row">
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
    </div> -->

</body>

</html>