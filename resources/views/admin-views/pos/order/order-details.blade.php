@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order Details'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0">
                <img src="{{asset('/public/assets/back-end/img/all-orders.png')}}" alt="">
                {{ \App\CPU\translate('Order_Details') }}
            </h2>
        </div>

        <!-- End Page Header -->

        <div class="row gx-2 gy-3" id="printableArea">
            <div class="col-lg-8 col-xl-9">
                <!-- Card -->
                <div class="card h-100">
                    <!-- Body -->
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-10 justify-content-between mb-4">
                            <div class="d-flex flex-column gap-10">
                                <h4 class="text-capitalize">{{\App\CPU\translate('Order_ID')}} #{{$order['id']}}</h4>
                                <div class="">
                                    <i class="tio-date-range"></i> {{date('d M Y H:i:s',strtotime($order['created_at']))}}
                                </div>
                            </div>
                            <div class="text-sm-right">
                                <div class="d-flex flex-wrap gap-10 justify-content-sm-end">
                                    <a class="btn btn--primary px-4" target="_blank"
                                    href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                        <i class="tio-print mr-1"></i> {{\App\CPU\translate('Print')}} {{\App\CPU\translate('invoice')}}
                                    </a>
                                </div>
                                <div class="d-flex flex-column gap-2 mt-3">
                                    <!-- Order status -->
                                    <div class="order-status d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">Status: </span>
                                        @if($order['order_status']=='pending')
                                            <span class="badge badge-soft-info font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{\App\CPU\translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{\App\CPU\translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                            <span class="badge badge-soft-warning font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{\App\CPU\translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @elseif($order['order_status']=='delivered' || $order['order_status']=='confirmed')
                                            <span class="badge badge-soft-success font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{\App\CPU\translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger font-weight-bold radius-50 d-flex align-items-center py-1 px-2">
                                                {{\App\CPU\translate(str_replace('_',' ',$order['order_status']))}}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Payment Method -->
                                    <div class="payment-method d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{\App\CPU\translate('Payment')}} {{\App\CPU\translate('Method')}} :</span>
                                        <strong> {{str_replace('_',' ',$order['payment_method'])}}</strong>
                                    </div>

                                    <!-- reference-code -->
                                    <div class="reference-code d-flex justify-content-sm-end gap-10 text-capitalize">
                                        <span class="title-color">{{\App\CPU\translate('Reference')}} {{\App\CPU\translate('Code')}} :</span>
                                        <strong>{{str_replace('_',' ',$order['transaction_ref'])}}</strong>
                                    </div>

                                    <!-- Payment Status -->
                                    <div class="payment-status d-flex justify-content-sm-end gap-10">
                                        <span class="title-color">Payment Status:</span>
                                        @if($order['payment_status']=='paid')
                                            <span class="text-success font-weight-bold">
                                                {{\App\CPU\translate('Paid')}}
                                            </span>
                                        @else
                                            <span class="text-danger font-weight-bold">
                                                {{\App\CPU\translate('Unpaid')}}
                                            </span>
                                        @endif
                                    </div>

                                    @if(\App\CPU\Helpers::get_business_settings('order_verification'))
                                        <span class="ml-2 ml-sm-3">
                                            <b>
                                                {{\App\CPU\translate('order_verification_code')}} : {{$order['verification_code']}}
                                            </b>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive datatable-custom">
                            <table class="table fz-12 table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{\App\CPU\translate('SL')}}</th>
                                        <th>{{\App\CPU\translate('Item_Details')}}</th>
                                        <th>UUID</th>
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
                                            
                                            @if(!empty($detail['mac_ids']['uuid']))
                                                @foreach($detail['mac_ids']['uuid'] as $k => $val)
                                                  {{$val}}<br>
                                                @endforeach
                                            @endif
                                            
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <?php
                            if ($order['extra_discount_type'] == 'percent') {
                                $extra_discount = 0; //($total_product_price / 100) * $order['extra_discount'];
                            } else {
                                $extra_discount = $order['extra_discount'];
                            }
                            if(isset($order['discount_amount'])){
                                $coupon_discount =$order['discount_amount'];
                            }
                        ?>
                        <div class="row justify-content-md-end mb-3">
                            <div class="col-md-9 col-lg-8">
                                <dl class="row text-sm-right">

                                    {{-- <dt class="col-sm-6">{{\App\CPU\translate('extra_discount')}}</dt>
                                    <dd class="col-sm-6 title-color">
                                        <strong>- {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($extra_discount))}}</strong>
                                    </dd>

                                    <dt class="col-sm-6">{{\App\CPU\translate('coupon_discount')}}</dt>
                                    <dd class="col-sm-6 title-color">
                                        <strong>- {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($coupon_discount))}}</strong>
                                    </dd> --}}

                                    <dt class="col-sm-6">{{\App\CPU\translate('Total Amount')}}</dt>
                                    <dd class="col-sm-6 title-color">
                                        <strong>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order['order_amount']))}}</strong>
                                    </dd>
                                </dl>
                                <!-- End Row -->
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4 col-xl-3">
                <!-- Card -->
                <div class="card">
                    <!-- Body -->
                    @if($order->customer)
                        <div class="card-body">
                            <h4 class="mb-4 d-flex align-items-center gap-2">
                                <img src="{{asset('/public/assets/back-end/img/seller-information.png')}}" alt="">
                                {{\App\CPU\translate('Customer_information')}}
                            </h4>

                            <div class="media flex-wrap gap-3">
                                <div class="">
                                    <img
                                        class="avatar rounded-circle avatar-70" style="width: 75px;height: 42px"
                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                        src="{{asset('storage/app/public/profile/'.$order->customer->image)}}"
                                        alt="Image">
                                </div>
                                <div class="media-body d-flex flex-column gap-1">
                                    <span class="title-color hover-c1"><strong>{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong></span>
                                    <span class="title-color">
                                        <strong>{{$total_orders}} </strong>{{\App\CPU\translate('Device')}}
                                    </span>
                                    <span class="title-color break-all"><strong>{{$order->customer['phone']}}</strong></span>
                                    <span class="title-color break-all">{{$order->customer['email']}}</span>
                                </div>
                                <div class="media-body text-right">
                                    {{--<i class="tio-chevron-right text-body"></i>--}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <img src="{{asset('/public/assets/back-end/img/seller-information.png')}}" alt="">
                                {{\App\CPU\translate('shipping_address')}}
                            </h4>
                               
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span>{{\App\CPU\translate('Name')}} :</span>
                                    <strong>{{$order->customer->shipping_name ? $order->customer->shipping_name : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('Contact')}}:</span>
                                    <strong>{{$order->customer->shipping_phone ? $order->customer->shipping_phone  : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('City')}}:</span>
                                    <strong>{{$order->customer->shipping_city ? $order->customer->shipping_city : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('zip_code')}} :</span>
                                    <strong>{{$order->customer->shipping_zip ? $order->customer->shipping_zip  : ''}}</strong>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <img src="{{asset('/public/assets/back-end/img/location.png')}}" alt="">
                                    {{$order->customer->shipping_city ? $order->customer->shipping_city  : \App\CPU\translate('empty')}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h4 class="mb-4 d-flex gap-2">
                                <img src="{{asset('/public/assets/back-end/img/seller-information.png')}}" alt="">
                                {{\App\CPU\translate('billing_address')}}
                            </h4>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <span>{{\App\CPU\translate('Name')}} :</span>
                                    <strong>{{$order->customer->name ? $order->customer->name : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('Contact')}}:</span>
                                    <strong>{{$order->customer->phone ? $order->customer->phone_code . " " . $order->customer->phone  : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('City')}}:</span>
                                    <strong>{{$order->customer->city ? $order->customer->city : ''}}</strong>
                                </div>
                                <div>
                                    <span>{{\App\CPU\translate('zip_code')}} :</span>
                                    <strong>{{$order->customer->zip ? $order->customer->zip  : ''}}</strong>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <img src="{{asset('/public/assets/back-end/img/location.png')}}" alt="">
                                    {{$order->customer->city ? $order->customer->city  : ''}}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card-body">
                            <div class="media align-items-center">
                                <span>{{\App\CPU\translate('no_customer_found')}}</span>
                            </div>
                        </div>
                    @endif
                <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <!-- End Row -->
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
