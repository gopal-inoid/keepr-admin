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
                {{ \App\CPU\translate('Order_Details') }} For {{\App\CPU\translate('Order_ID')}} #{{$order['id']}}
            </h2>
        </div>
        <div class="row gx-2 gy-3" id="printableArea">
            <div class="col-lg-12 col-xl-12">
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
                                            <input type="date" name="order_date" class="form-control" value="" placeholder="{{ \App\CPU\translate('Order Date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="title-color">Order Note</label>
                                            <textarea class="form-control" placeholder="{{ \App\CPU\translate('Order Note') }}"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12">
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
                                            <input type="text" name="billing_name" class="form-control" value="" placeholder="{{ \App\CPU\translate('Name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Address</label>
                                            <input type="text" name="billing_address" class="form-control" value="" placeholder="{{ \App\CPU\translate('Address') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">City</label>
                                            <input type="text" name="billing_city" class="form-control" value="" placeholder="{{ \App\CPU\translate('City') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">State</label>
                                            <input type="text" name="billing_state" class="form-control" value="" placeholder="{{ \App\CPU\translate('State') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Country</label>
                                            <input type="text" name="billing_country" class="form-control" value="" placeholder="{{ \App\CPU\translate('Country') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">zipcode</label>
                                            <input type="text" name="billing_zip" class="form-control" value="" placeholder="{{ \App\CPU\translate('Name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Phone</label>
                                            <input type="text" name="billing_phone" class="form-control" value="" placeholder="{{ \App\CPU\translate('Phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-xl-12">
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
                                                <input type="checkbox" onchange="" data-id="country_area" class="status switcher_input" {{ isset($country_restriction_status->value) && $country_restriction_status->value  == 1 ? 'checked' : '' }}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Name</label>
                                            <input type="text" name="shipping_name" class="form-control" value="" placeholder="{{ \App\CPU\translate('Name') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Address</label>
                                            <input type="text" name="shipping_address" class="form-control" value="" placeholder="{{ \App\CPU\translate('Address') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">City</label>
                                            <input type="text" name="shipping_city" class="form-control" value="" placeholder="{{ \App\CPU\translate('City') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">State</label>
                                            <input type="text" name="shipping_state" class="form-control" value="" placeholder="{{ \App\CPU\translate('State') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Country</label>
                                            <input type="text" name="shipping_country" class="form-control" value="" placeholder="{{ \App\CPU\translate('Country') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Zipcode</label>
                                            <input type="text" name="shipping_zip" class="form-control" value="" placeholder="{{ \App\CPU\translate('Zipcode') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="title-color">Phone</label>
                                            <input type="text" name="shipping_phone" class="form-control" value="" placeholder="{{ \App\CPU\translate('Phone') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
