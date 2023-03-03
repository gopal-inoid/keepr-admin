@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Order List'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div>
            <!-- Page Title -->
            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                <h2 class="h1 mb-0">
                    <img src="{{asset('/public/assets/back-end/img/all-orders.png')}}" class="mb-1 mr-1" alt="">
                    <span class="page-header-title">
                        @if($status =='processing')
                            {{ ucwords(str_replace('_',' ','Packaging' )) }}
                        @elseif($status =='failed')
                            {{ ucwords(str_replace('_',' ','Failed to Deliver' )) }}
                        @else
                            {{ ucwords(str_replace('_',' ',$status )) }}
                        @endif
                    </span>
                    {{\App\CPU\translate('Orders')}}
                </h2>
                <span class="badge badge-soft-dark radius-50 fz-14">{{$orders->total()}}</span>
            </div>
            <!-- End Page Title -->

            <!-- Order States -->
            <div class="card">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ url()->current() }}" id="form-data" method="GET">
                            <div class="row gy-3 gx-2">
                                <div class="col-12 pb-0">
                                    <h4>{{\App\CPU\translate('select')}} {{\App\CPU\translate('date')}} {{\App\CPU\translate('range')}}</h4>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <select name="filter" class="form-control">
                                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>All</option>
                                        <option value="type1" {{ $filter == 'type1' ? 'selected' : '' }}>type1</option>
                                        <option value="type2" {{ $filter == 'type2' ? 'selected' : '' }}>type2</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-floating">
                                        <input type="date" name="from" value="{{$from}}" id="from_date" class="form-control">
                                        <label>Start Date</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mt-2 mt-sm-0">
                                    <div class="form-floating">
                                        <input type="date" value="{{$to}}" name="to" id="to_date" class="form-control">
                                        <label>End Date</label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3 mt-2 mt-sm-0  ">
                                    <button type="submit" class="btn btn--primary btn-block" onclick="formUrlChange(this)" data-action="{{ url()->current() }}">
                                        {{\App\CPU\translate('show')}} {{\App\CPU\translate('data')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Data Table Top -->
                    <div class="px-3 py-4 light-bg">
                        <div class="row g-2 flex-grow-1">
                            <div class="col-sm-8 col-md-6 col-lg-4">
                                <form action="" method="GET">
                                    <!-- Search -->
                                    <div class="input-group input-group-custom input-group-merge">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="tio-search"></i>
                                            </div>
                                        </div>
                                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                                            placeholder="{{\App\CPU\translate('Search by Order ID')}}" aria-label="Search by Order ID" value="{{ $search }}"
                                            required>
                                        <button type="submit" class="btn btn--primary input-group-text">{{\App\CPU\translate('search')}}</button>
                                    </div>
                                    <!-- End Search -->
                                </form>
                            </div>
                        </div>
                        <!-- End Row -->
                    </div>
                    <!-- End Data Table Top -->

                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100"
                            style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}}">
                            <thead class="thead-light thead-50 text-capitalize">
                                <tr>
                                    <th class="">{{\App\CPU\translate('SL')}}</th>
                                    <th>{{\App\CPU\translate('Order')}} {{\App\CPU\translate('ID')}}</th>
                                    <th>{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Date')}}</th>
                                    <th>{{\App\CPU\translate('customer')}} {{\App\CPU\translate('info')}}</th>
                                    <th>{{\App\CPU\translate('Store')}}</th>
                                    <th class="text-right">{{\App\CPU\translate('Total')}} {{\App\CPU\translate('Amount')}}</th>
                                    <th class="text-center">{{\App\CPU\translate('Order')}} {{\App\CPU\translate('Status')}} </th>
                                    <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)

                                <tr class="status-{{$order['order_status']}} class-all">
                                    <td class="">
                                        {{$orders->firstItem()+$key}}
                                    </td>
                                    <td >
                                        <a class="title-color" href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td>
                                        <div>{{date('d M Y',strtotime($order['created_at']))}},</div>
                                        <div>{{ date("h:i A",strtotime($order['created_at'])) }}</div>
                                    </td>
                                    <td>
                                        @if($order->customer_id == 0)
                                            <strong class="title-name">Walking customer</strong>
                                        @else
                                            @if($order->customer)
                                                <a class="text-body text-capitalize" href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                    <strong class="title-name">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</strong>
                                                </a>
                                                <a class="d-block title-color" href="tel:{{ $order->customer['phone'] }}">{{ $order->customer['phone'] }}</a>
                                            @else
                                                <label class="badge badge-danger fz-12">{{\App\CPU\translate('invalid_customer_data')}}</label>
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <td class="text-right">
                                        <div>{{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount))}}</div>

                                        @if($order->payment_status=='paid')
                                            <span class="badge text-success fz-12 px-0">
                                                {{\App\CPU\translate('paid')}}
                                            </span>
                                        @else
                                            <span class="badge text-danger fz-12 px-0">
                                                {{\App\CPU\translate('unpaid')}}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center text-capitalize">
                                        @if($order['order_status']=='pending')
                                            <span class="badge badge-soft-info fz-12">
                                                    {{$order['order_status']}}
                                            </span>

                                        @elseif($order['order_status']=='processing' || $order['order_status']=='out_for_delivery')
                                            <span class="badge badge-soft-warning fz-12">
                                                {{str_replace('_',' ',$order['order_status'] == 'processing' ? 'packaging':$order['order_status'])}}
                                            </span>
                                        @elseif($order['order_status']=='confirmed')
                                            <span class="badge badge-soft-success fz-12">
                                                {{$order['order_status']}}
                                            </span>
                                        @elseif($order['order_status']=='failed')
                                            <span class="badge badge-danger fz-12">
                                                {{$order['order_status'] == 'failed' ? 'Failed To Deliver' : ''}}
                                            </span>
                                        @elseif($order['order_status']=='delivered')
                                            <span class="badge badge-soft-success fz-12">
                                                {{$order['order_status']}}
                                            </span>
                                        @else
                                            <span class="badge badge-soft-danger fz-12">
                                                {{$order['order_status']}}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a class="btn btn-outline--primary square-btn btn-sm mr-1" title="{{\App\CPU\translate('view')}}"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}">
                                                <img src="{{asset('/public/assets/back-end/img/eye.svg')}}" class="svg" alt="">
                                            </a>
                                            <a class="btn btn-outline-success square-btn btn-sm mr-1" target="_blank" title="{{\App\CPU\translate('invoice')}}"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-download-to"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- End Table -->

                    <!-- Pagination -->
                    <div class="table-responsive mt-4">
                        <div class="d-flex justify-content-lg-end">
                            <!-- Pagination -->
                            {!! $orders->links() !!}
                        </div>
                    </div>
                    <!-- End Pagination -->
                </div>
            </div>
            <!-- End Order States -->

            <!-- Nav Scroller -->
            <div class="js-nav-scroller hs-nav-scroller-horizontal d-none">
                <span class="hs-nav-scroller-arrow-prev d-none">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-left"></i>
                    </a>
                </span>

                <span class="hs-nav-scroller-arrow-next d-none">
                    <a class="hs-nav-scroller-arrow-link" href="javascript:;">
                        <i class="tio-chevron-right"></i>
                    </a>
                </span>

                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">{{\App\CPU\translate('order_list')}}</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
            <!-- End Nav Scroller -->
        </div>
        <!-- End Page Header -->
    </div>
@endsection

@push('script_2')
    <script>
        function filter_order() {
            $.get({
                url: '{{route('admin.orders.inhouse-order-filter')}}',
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success('{{\App\CPU\translate('order_filter_success')}}');
                    location.reload();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        };
    </script>
    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if(fr != ''){
                $('#to_date').attr('required','required');
            }
            if(to != ''){
                $('#from_date').attr('required','required');
            }
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{\App\CPU\translate('Invalid date range')}}!', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }

        })
    </script>
@endpush
