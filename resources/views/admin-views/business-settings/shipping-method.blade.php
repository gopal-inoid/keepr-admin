@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Shipping Methods'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
            <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
            Shipping Methods
            <span class="badge badge-soft-dark radius-50 fz-14 ml-1">{{ $pro->total() }}</span>
        </h2>
    </div>
    <!-- End Page Title -->

    <div class="row pb-4 d--none" id="main-add-shipping-method" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 text-capitalize">{{ \App\CPU\translate('Add Shipping Methods')}}</h5>
                </div>
                <div class="card-body">
                    <form class="product-form" action="{{ route('admin.business-settings.save-shipping-method') }}" method="POST"
                        style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                        enctype="multipart/form-data" id="product_form">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name" class="title-color">{{ \App\CPU\translate('Type') }}</label>
                                    <select name="product_type" id="product_type" class="form-control" required>
                                        <option value="type1" selected>{{ \App\CPU\translate('type1') }}</option>
                                        <option value="type2" selected>{{ \App\CPU\translate('type2') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-4 form-group">
                                        <label class="title-color">{{ \App\CPU\translate('Method Name') }}</label>
                                        <input type="text" placeholder="Method Name" name="method_name"
                                            value="{{ old('method_name') }}" class="form-control" required>
                                </div>
                                <div class="mb-12 d-flex align-items-center gap-2">
                                    <label for="colors" class="title-color mb-0">
                                        {{ \App\CPU\translate('Status') }} :
                                    </label>
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher_input" value="{{ old('status') }}"
                                            name="status">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end gap-3 mt-3">
                            <button type="reset" class="cancel btn btn-secondary">{{ \App\CPU\translate('reset') }}</button>
                            <button type="button" onclick="check()" class="btn btn--primary">{{ \App\CPU\translate('Submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <!-- Search -->
                            <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                           placeholder="{{\App\CPU\translate('Search Product Name')}}" aria-label="Search orders"
                                           value="{{ $search }}" required>
                                    <input type="hidden" value="{{ $request_status }}" name="status">
                                    <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                </div>
                            </form>
                            <!-- End Search -->
                        </div>
                        <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                            <a href="javascript:void(0);" id="add-shiping-method" class="btn btn--primary">
                                <i class="tio-add"></i>
                                <span class="text">{{\App\CPU\translate('Add Shipping Method')}}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('SL')}}</th>
                                <th>{{\App\CPU\translate('Product Name')}}</th>
                                <th class="text-right">{{\App\CPU\translate('Product Type')}}</th>
                                <th class="text-right">{{\App\CPU\translate('purchase_price')}}</th>
                                <th class="text-right">{{\App\CPU\translate('selling_price')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Show_as_featured')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Active')}} {{\App\CPU\translate('status')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pro as $k=>$p)
                            <tr>
                                <th scope="row">{{$pro->firstItem()+$k}}</th>
                                <td>
                                    <a href="{{route('admin.product.view',[$p['id']])}}" class="media align-items-center gap-2">
                                        <img src="{{\App\CPU\ProductManager::product_image_path('thumbnail')}}/{{$p['thumbnail']}}"
                                             onerror="this.src='{{asset('/public/assets/back-end/img/brand-logo.png')}}'"class="avatar border" alt="">
                                        <span class="media-body title-color hover-c1">
                                            {{\Illuminate\Support\Str::limit($p['name'],20)}}
                                        </span>
                                    </a>
                                </td>
                                <td class="text-right">
                                    {{ ucfirst($p['product_type']) }}
                                </td>
                                <td class="text-right">
                                    {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['purchase_price']))}}
                                </td>
                                <td class="text-right">
                                    {{\App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($p['unit_price']))}}
                                </td>
                                <td class="text-center">
                                    <label class="mx-auto switcher">
                                        <input class="switcher_input" type="checkbox"
                                                onclick="featured_status('{{$p['id']}}')" {{$p->featured == 1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <label class="mx-auto switcher">
                                        <input type="checkbox" class="status switcher_input"
                                                id="{{$p['id']}}" {{$p->status == 1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline-info btn-sm square-btn" title="{{ \App\CPU\translate('barcode') }}"
                                            href="{{ route('admin.product.barcode', [$p['id']]) }}">
                                            <i class="tio-barcode"></i>
                                        </a>
                                        <a class="btn btn-outline-info btn-sm square-btn" title="View" href="{{route('admin.product.view',[$p['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                        <a class="btn btn-outline--primary btn-sm square-btn"
                                            title="{{\App\CPU\translate('Edit')}}"
                                            href="{{route('admin.product.edit',[$p['id']])}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger btn-sm square-btn" href="javascript:"
                                            title="{{\App\CPU\translate('Delete')}}"
                                            onclick="form_alert('product-{{$p['id']}}','Want to delete this item ?')">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.product.delete',[$p['id']])}}"
                                            method="post" id="product-{{$p['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {{$pro->links()}}
                    </div>
                </div>

                @if(count($pro)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <!-- Page level plugins -->
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('public/assets/back-end')}}/vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Page level custom scripts -->
    <script>

        $('#add-shiping-method').on('click', function () {
            $('#main-add-shipping-method').show();
        });

        $('.cancel').on('click', function () {
            $('#main-add-shipping-method').hide();
        });

        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });

        $(document).on('change', '.status', function () {
            var id = $(this).attr("id");
            if ($(this).prop("checked") == true) {
                var status = 1;
            } else if ($(this).prop("checked") == false) {
                var status = 0;
            }
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.status-update')}}",
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                success: function (data) {
                    if(data.success == true) {
                        toastr.success('{{\App\CPU\translate('Status updated successfully')}}');
                    }
                    else if(data.success == false) {
                        toastr.error('{{\App\CPU\translate('Status updated failed. Product must be approved')}}');
                        setTimeout(function(){
                            location.reload();
                        }, 2000);
                    }
                }
            });
        });

        function featured_status(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.product.featured-status')}}",
                method: 'POST',
                data: {
                    id: id
                },
                success: function () {
                    toastr.success('{{\App\CPU\translate('Featured status updated successfully')}}');
                }
            });
        }

    </script>
@endpush
