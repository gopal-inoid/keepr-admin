@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    .phone_code > .form-group {
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        width: auto !important;
    }
    .phone_code > .form-group:focus {
        color: #212529;
        background-color: #fff;
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgb(13 110 253 / 25%);
    }
    .phone_code > .form-group input {
        display: inline-block !important;
        width: auto !important;
        border: none !important;
    }
    .phone_code > .form-group input:focus {
        box-shadow: none !important;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img src="{{asset('/public/assets/back-end/img/all-orders.png')}}" alt="">
                {{ \App\CPU\translate('Order_Details') }} For {{\App\CPU\translate('Order_ID')}} #{{$order['id']}}
            </h2>
        </div>
        <div class="row gx-2 gy-3" id="printableArea">
            <form class="" action="{{ route('admin.orders.update-order-details') }}" method="POST" id="order_detail_form">
                @csrf
                <input type="hidden" name="order_id" value="{{$order['id']}}">
                <input type="hidden" name="user_id" value="{{$order->customer->id}}">
                <div class="col-lg-12 col-xl-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h4 mb-0">{{ \App\CPU\translate('Order_info') }}</h3>
                            <div class="d-flex flex-wrap gap-10 justify-content-sm-end">
                                <a class="btn btn--primary px-4" target="_blank"
                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                    <i class="tio-print mr-1"></i> {{\App\CPU\translate('Print')}} {{\App\CPU\translate('invoice')}}
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color">Order Status</label>
                                                <select class="form-control js-select2-custom" id="change_order_status" order_id="{{$order['id']}}" name="change_order_status">
                                                    <option {{($order['order_status'] == 'pending' ? 'selected' : '')}} value="pending">Pending</option>
                                                    <option {{($order['order_status'] == 'processing' ? 'selected' : '')}} value="processing">Processing</option>
                                                    <option {{($order['order_status'] == 'shipped' ? 'selected' : '')}} value="shipped">Shipped</option>
                                                    <option {{($order['order_status'] == 'delivered' ? 'selected' : '')}} value="delivered">Delivered</option>
                                                    <option {{($order['order_status'] == 'cancelled' ? 'selected' : '')}} value="cancelled">Cancelled</option>
                                                    <option {{($order['order_status'] == 'refunded' ? 'selected' : '')}} value="refunded">Refunded</option>
                                                    <option {{($order['order_status'] == 'failed' ? 'selected' : '')}} value="failed">Failed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="title-color">Order Date</label>
                                                <input type="date" name="order_date" class="form-control" value="{{date('Y-m-d',strtotime($order['created_at']))}}" placeholder="{{ \App\CPU\translate('Order Date') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="title-color">Order Note</label>
                                                <textarea class="form-control" name="order_note" placeholder="{{ \App\CPU\translate('Order Note') }}">{{$order['order_note'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($order->customer)
                    <div class="col-lg-12 col-xl-12 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="h4 mb-0">{{ \App\CPU\translate('Customer Billing Detail') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Name</label>
                                                    <input type="text" name="billing_name" class="form-control" value="{{$order->customer['name']}}" placeholder="{{ \App\CPU\translate('Name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Email</label>
                                                    <input type="text" name="email" class="form-control" value="{{$order->customer['email'] ?? ''}}" placeholder="{{ \App\CPU\translate('Email') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Address</label>
                                                    <input type="text" name="street_address" class="form-control" value="{{$order->customer['street_address']}}" placeholder="{{ \App\CPU\translate('Address') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">City</label>
                                                    <input type="text" name="billing_city" class="form-control" value="{{$order->customer['city']}}" placeholder="{{ \App\CPU\translate('City') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">State</label>
                                                    <select class="form-control js-select2-custom" id="billing_state" name="billing_state">
                                                        @foreach($states as $k => $val)
                                                            <option {{($order->customer['state'] == $val->id ? 'selected' : '')}} value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Country</label>
                                                    <select class="form-control js-select2-custom" id="billing_country" name="billing_country">
                                                        @foreach($countries as $k => $val)
                                                            <option {{($order->customer['country'] == $val->id ? 'selected' : '')}} value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">zipcode</label>
                                                    <input type="text" name="billing_zip" class="form-control" value="{{$order->customer['zip']}}" placeholder="{{ \App\CPU\translate('Name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3 phone_code">
                                                <label class="title-color">Phone</label>
                                                <div class="form-group">
                                                    <span class="border-end country-code px-2">{{$order->customer['billing_phone_code']}}</span>
                                                    <input type="number" class="form-control" value="{{$order->customer['billing_phone'] ?? ''}}" name="billing_phone" placeholder="{{ \App\CPU\translate('Phone') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-12 mb-3">
                        <div class="card h-100">
                            <div class="card-header">
                                <h3 class="h4 mb-0">{{ \App\CPU\translate('Customer Shipping Detail') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="">same billing adress</label>
                                                    <label class="switcher">
                                                        <input type="checkbox" name="is_billing_address_same" class="switcher_input" {{ isset($order->customer['is_billing_address_same']) && $order->customer['is_billing_address_same']  == 1 ? 'checked' : '' }}>
                                                        <span class="switcher_control"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Name</label>
                                                    <input type="text" name="shipping_name" class="form-control" value="{{$order->customer['shipping_name'] ?? ''}}" placeholder="{{ \App\CPU\translate('Name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Email</label>
                                                    <input type="text" name="shipping_email" class="form-control" value="{{$order->customer['shipping_email'] ?? ''}}" placeholder="{{ \App\CPU\translate('Email') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Address</label>
                                                    <input type="text" name="add_shipping_address" class="form-control" value="{{$order->customer['add_shipping_address'] ?? ''}}" placeholder="{{ \App\CPU\translate('Address') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">City</label>
                                                    <input type="text" name="shipping_city" class="form-control" value="{{$order->customer['shipping_city'] ?? ''}}" placeholder="{{ \App\CPU\translate('City') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">State</label>
                                                    <select class="form-control js-select2-custom" id="shipping_state" name="shipping_state">
                                                        @foreach($states as $k => $val)
                                                            <option {{($order->customer['state'] == $val->id ? 'selected' : '')}} value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Country</label>
                                                    <select class="form-control js-select2-custom" id="shipping_country" name="shipping_country">
                                                        @foreach($countries as $k => $val)
                                                            <option {{($order->customer['country'] == $val->id ? 'selected' : '')}} value="{{$val->id}}">{{$val->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="title-color">Zip</label>
                                                    <input type="text" name="shipping_zip" class="form-control" value="{{$order->customer['shipping_zip'] ?? ''}}" placeholder="{{ \App\CPU\translate('Zipcode') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3 phone_code">
                                                <label class="title-color">Phone</label>
                                                <div class="form-group">
                                                    <span class="border-end country-code px-2">{{$order->customer['shipping_phone_code']}}</span>
                                                    <input type="number" class="form-control" name="shipping_phone" value="{{$order->customer['shipping_phone'] ?? ''}}" placeholder="{{ \App\CPU\translate('Phone') }}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-lg-12 col-xl-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h4 mb-0">{{ \App\CPU\translate('Shipment Detail') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="">Shipping type</label>
                                                <select class="form-control" id="shipping_mode" name="shipping_mode">
                                                    <option {{($order['normal_rate'] == 'normal_rate' ? 'selected' : '')}} value="normal_rate">Normal Rate</option>
                                                    <option {{($order['express_rate'] == 'express_rate' ? 'selected' : '')}} value="express_rate">Express Rate</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="title-color">Tracking ID</label>
                                                <input type="text" name="tracking_id" class="form-control" value="{{$order['tracking_id']}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="title-color">Estimated Delivery Date</label>
                                                <input type="date" name="expected_delivery_date" class="form-control" value="{{date('Y-m-d',strtotime($order['expected_delivery_date']))}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="">Shipment information</label>
                                                <textarea name="shipment_info" class="form-control">{{ $order['shipment_info'] ?? "" }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h4 mb-0">{{ \App\CPU\translate('Payment Detail') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="title-color">Payment Transaction ID</label>
                                                <input type="text" name="transaction_ref" class="form-control" value="{{$order['transaction_ref'] ?? ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="">Payment Method</label>
                                                <select class="form-control" id="payment_method" name="payment_method">
                                                    <option {{($order['payment_method'] == 'Stripe' ? 'selected' : '')}} value="Stripe">Stripe</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="title-color">Payment Status</label>
                                                <select class="form-control js-select2-custom" id="payment_status" name="payment_status">
                                                    <option {{($order['payment_status'] == 'paid' ? 'selected' : '')}} value="paid">Paid</option>
                                                    <option {{($order['payment_status'] == 'unpaid' ? 'selected' : '')}} value="unpaid">Unpaid</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-12 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="h4 mb-0">{{ \App\CPU\translate('Product Detail') }}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive datatable-custom">
                                <table class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                    <thead class="thead-light thead-50 text-capitalize">
                                        <tr>
                                            <th>{{\App\CPU\translate('SL')}}</th>
                                            <th>{{\App\CPU\translate('Product Name')}}</th>
                                            <th>Device Info</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total Amount</th>
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
                                                    <div>
                                                        <a href="#" class="title-color hover-c1"><h6>{{substr($detail['name'],0,30)}}{{strlen($detail['name'])>10?'...':''}}</h6></a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(!empty($detail['mac_ids']))
                                                    @foreach($detail['mac_ids'] as $k => $val)
                                                        <strong>UUID: </strong>{{$val['uuid']}}<br />
                                                        <strong>Major: </strong>{{$val['major']}}<br />
                                                        <strong>Minor: </strong>{{$val['minor']}}<br />
                                                        <hr />
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @php($total_price = 0)
                                                @if(!empty($detail['mac_ids']))
                                                    @foreach($detail['mac_ids'] as $val)
                                                    @php($total_price += $detail['price'])
                                                    <br />${{$detail['price'] ?? ''}}<br /><br /><hr />
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>{{count($detail['mac_ids'])}}</td>
                                            <td>${{$total_price}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row justify-content-md-end mb-3">
                                <div class="col-md-12 col-lg-12">
                                    <table class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                        <thead class="thead-light thead-50 text-capitalize">
                                            <tr>
                                                <th>{{\App\CPU\translate('Other info')}}</th>
                                                <th class="text-right"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label><strong>{{\App\CPU\translate('Tax info')}}</strong>: </label>
                                                    @php($tx_amt = $ship_amt = 0)
                                                    @foreach($tax_info as $product_id => $taxes)
                                                        @foreach($taxes as $k1 => $tax)
                                                            @php($tx_amt += $tax['amount'])
                                                                <strong>{{$tax['title']}}</strong><br />
                                                        @endforeach
                                                    @endforeach
                                                </td>
                                                <td class="text-right"><strong>${{number_format($tx_amt,2)}}</strong></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label><strong>{{\App\CPU\translate('Shipping info')}}</strong>: </label><br />
                                                    @foreach($shipping_info as $product_id => $shipping)
                                                            @php($ship_amt += $shipping['amount'])
                                                            <strong>Shipping Co.: {{$shipping['title']}}</strong><br />
                                                            <strong>Duration: {{$shipping['duration']}}</strong><br />
                                                            <strong>Shipping Mode: {{$shipping['mode']}}</strong>
                                                    @endforeach
                                                </td>
                                                <td class="text-right">
                                                    <strong>${{number_format($ship_amt,2)}}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><h4><strong>Grand Total</strong></h4></td>
                                                <td class="text-right">
                                                    <h4><strong>${{$total_order_amount}}</strong></h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-12">
                    <div class="float-right gap-3 mt-3">
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update Information') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--Show locations on map Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"
                        id="locationModalLabel">{{\App\CPU\translate('location')}} {{\App\CPU\translate('data')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map">
                                <div style="width: 100%; height: 400px;" id="location_map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
@endsection

@push('script_2')
    <script>
        $(document).on('change', '.payment_status', function () {
            var id = $(this).attr("data-id");
            var value = $(this).val();
            Swal.fire({
                title: '{{\App\CPU\translate('Are you sure Change this')}}?',
                text: "{{\App\CPU\translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.payment-status')}}",
                        method: 'POST',
                        data: {
                            "id": id,
                            "payment_status": value
                        },
                        success: function (data) {
                            toastr.success('{{\App\CPU\translate('Status Change successfully')}}');
                            location.reload();
                        }
                    });
                }
            })
        });

        function order_status(status) {
            @if($order['order_status']=='delivered')
            Swal.fire({
                title: '{{\App\CPU\translate('Order is already delivered, and transaction amount has been disbursed, changing status can be the reason of miscalculation')}}!',
                text: "{{\App\CPU\translate('Think before you proceed')}}.",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
            @else
            Swal.fire({
                title: '{{\App\CPU\translate('Are you sure Change this')}}?',
                text: "{{\App\CPU\translate('You will not be able to revert this')}}!",
                showCancelButton: true,
                confirmButtonColor: '#377dff',
                cancelButtonColor: 'secondary',
                confirmButtonText: '{{\App\CPU\translate('Yes, Change it')}}!'
            }).then((result) => {
                if (result.value) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: "{{route('admin.orders.status')}}",
                        method: 'POST',
                        data: {
                            "id": '{{$order['id']}}',
                            "order_status": status
                        },
                        success: function (data) {
                            if (data.success == 0) {
                                toastr.success('{{\App\CPU\translate('Order is already delivered, You can not change it')}} !!');
                                location.reload();
                            } else {
                                toastr.success('{{\App\CPU\translate('Status Change successfully')}}!');
                                location.reload();
                            }

                        }
                    });
                }
            })
            @endif
        }
    </script>

    <script>
        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: {
                    'order_id': '{{$order['id']}}',
                    'delivery_man_id': id
                },
                success: function (data) {
                    if (data.status == true) {
                        toastr.success('Delivery man successfully assigned/changed', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        toastr.error('Deliveryman man can not assign/change in that status', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                },
                error: function () {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })

        function waiting_for_location() {
            toastr.warning('{{\App\CPU\translate('waiting_for_location')}}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

@endpush
