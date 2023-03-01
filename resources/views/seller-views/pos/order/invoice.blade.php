<style>
    @media print {
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #677788;
            border-collapse: collapse;
        }

        .table td, .table th {
            padding: .75rem;
            vertical-align: top;
            border-top: .0625rem solid rgba(231, 234, 243, .7)
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: .125rem solid rgba(231, 234, 243, .7)
        }

        .table tbody + tbody {
            border-top: .125rem solid rgba(231, 234, 243, .7)
        }

        .table-sm td, .table-sm th {
            padding: .3rem
        }

        .table-bordered {
            border: .0625rem solid rgba(231, 234, 243, .7)
        }

        .table-bordered td, .table-bordered th {
            border: .0625rem solid rgba(231, 234, 243, .7)
        }

        .table-bordered thead td, .table-bordered thead th {
            border-bottom-width: .125rem
        }
        .text-left {
            text-align: left !important;
        }
        .text-right {
            text-align: right !important;
        }
    }
</style>

<div style="width:410px;">
    <div class="text-center pt-4 mb-3">
        <h2 style="line-height: 1">{{\App\Model\BusinessSetting::where(['type'=>'company_name'])->first()->value}}</h2>
        {{-- <h5 style="font-size: 20px;font-weight: lighter;line-height: 1">
            {{\App\Model\BusinessSetting::where(['type'=>'address'])->first()->value}}
        </h5> --}}
        <h5 style="font-size: 16px;font-weight: lighter;line-height: 1">
            {{\App\CPU\translate('Phone')}}
            : {{\App\Model\BusinessSetting::where(['type'=>'company_phone'])->first()->value}}
        </h5>
    </div>

    <span>--------------------------------------------------------------------------------------</span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>{{\App\CPU\translate('Order ID')}} : {{$order['id']}}</h5>
        </div>
        <div class="col-6">
            <h5 style="font-weight: lighter">
                {{date('d/M/Y h:i a',strtotime($order['created_at']))}}
            </h5>
        </div>
        @if($order->customer)
            <div class="col-12">
                <h5>{{\App\CPU\translate('Customer Name')}} : {{$order->customer['f_name'].' '.$order->customer['l_name']}}</h5>
                @if ($order->customer->id !=0)
                    <h5>{{\App\CPU\translate('Phone')}} : {{$order->customer['phone']}}</h5>
                @endif

            </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span>--------------------------------------------------------------------------------------</span>
    <table class="table table-bordered mt-3 text-left" style="width: 100%!important">
        <thead>
        <tr>
            <th style="width: 80px">{{\App\CPU\translate('QTY')}}</th>
            <th class="text-left">{{\App\CPU\translate('DESC')}}</th>
            <th class="text-right">{{\App\CPU\translate('Price')}}</th>
        </tr>
        </thead>

        <tbody>
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_dis_on_pro=0)
        @php($product_price=0)
        @php($total_product_price=0)
        @php($ext_discount=0)
        @php($coupon_discount=0)
        @foreach($order->details as $detail)
            @if($detail->product)

                <tr>
                    <td style="width: 80px">
                        {{$detail['qty']}}
                    </td>
                    <td class="text-left">
                        <span> {{ Str::limit($detail->product['name'], 200) }}</span><br>
                        @if(count(json_decode($detail['variation'],true))>0)
                            <strong><u>{{\App\CPU\translate('Variation')}} : </u></strong>
                            @foreach(json_decode($detail['variation'],true) as $key1 =>$variation)
                                <div class="font-size-sm text-body" style="color: black!important;">
                                    <span>{{$key1}} :  </span>
                                    <span
                                        class="font-weight-bold">{{$variation}} </span>
                                </div>
                            @endforeach
                        @endif



                        {{\App\CPU\translate('Discount')}} : {{\App\CPU\Helpers::currency_converter(round($detail['discount'],2))}}
                    </td>
                    <td class="text-right">
                        @php($amount=($detail['price']*$detail['qty'])-$detail['discount'])
                        @php($product_price = $detail['price']*$detail['qty'])
                        {{\App\CPU\Helpers::currency_converter(round($amount,2))}}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_product_price+=$product_price)
                @php($total_tax+=$detail['tax'])

            @endif
        @endforeach
        </tbody>
    </table>
    <span>---------------------------------------------------------------------------------------</span>
    <?php


    if ($order['extra_discount_type'] == 'percent') {
        $ext_discount = ($total_product_price / 100) * $order['extra_discount'];
    } else {
        $ext_discount = $order['extra_discount'];
    }
    if(isset($order['discount_amount'])){
        $coupon_discount =$order['discount_amount'];
    }
    ?>
    <table style="color: black!important; width: 100%!important">
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{\App\CPU\translate('Items Price')}}:</td>
            <td class="text-right">{{\App\CPU\Helpers::currency_converter(round($sub_total,2))}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{\App\CPU\translate('Tax')}} / {{\App\CPU\translate('VAT')}}:</td>
            <td class="text-right">{{\App\CPU\Helpers::currency_converter(round($total_tax,2))}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{\App\CPU\translate('Subtotal')}}:</td>
            <td class="text-right">{{\App\CPU\Helpers::currency_converter(round($sub_total+$total_tax,2))}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{\App\CPU\translate('extra_discount')}}:</td>
            <td class="text-right">{{\App\CPU\Helpers::currency_converter(round($ext_discount,2))}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right">{{\App\CPU\translate('coupon_discount')}}:</td>
            <td class="text-right">{{\App\CPU\Helpers::currency_converter(round($coupon_discount,2))}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td class="text-right" style="font-size: 20px">{{\App\CPU\translate('Total')}}:</td>
            <td class="text-right" style="font-size: 20px">{{\App\CPU\Helpers::currency_converter(round($order->order_amount,2))}}</td>
        </tr>
    </table>


    <div class="d-flex flex-row justify-content-between border-top">
        <span>{{\App\CPU\translate('Paid_by')}}: {{\App\CPU\translate($order->payment_method)}}</span>
    </div>
    <span>---------------------------------------------------------------------------------------</span>
    <h5 class="text-center pt-3">
        """{{\App\CPU\translate('THANK YOU')}}"""
    </h5>
    <span>---------------------------------------------------------------------------------------</span>
</div>
