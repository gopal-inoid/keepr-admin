@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Stock List'))

@push('css_or_js')

@endpush

@section('content')

<div class="content container-fluid">
    {{$pro}}
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
            <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
             Product Stock List
        </h2>
    </div>
    <!-- End Page Title -->

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="px-3 py-4">
                    <div class="row align-items-center">
                        <div class="col-lg-4">
                            <!-- Search -->
                            {{-- <form action="{{ url()->current() }}" method="GET">
                                <div class="input-group input-group-custom input-group-merge">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="tio-search"></i>
                                        </div>
                                    </div>
                                    <input id="datatableSearch_" type="search" name="search" class="form-control"
                                           placeholder="{{\App\CPU\translate('Search Product Name')}}" aria-label="Search orders"
                                           value="{{ $search }}" required>
                                    <button type="submit" class="btn btn--primary">{{\App\CPU\translate('search')}}</button>
                                </div>
                            </form> --}}
                            <!-- End Search -->
                        </div>
                        <div class="col-lg-8 mt-3 mt-lg-0 d-flex flex-wrap gap-3 justify-content-lg-end">
                            <a href="javascript:void(0);" class="btn btn-outline--primary" data-toggle="modal" data-target="#importModal">
                                <i class="tio-update"></i>
                                <span class="text">{{\App\CPU\translate('Import')}}</span>
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline--primary" data-toggle="dropdown">
                                    <i class="tio-download-to"></i>
                                    Export
                                    <i class="tio-chevron-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li><a class="dropdown-item" href="{{route('admin.product.stocks.export-excel')}}">Excel</a></li>
                                    <div class="dropdown-divider"></div>
                                </ul>
                            </div>
                            <a href="{{route('admin.product.stocks.add-new')}}" class="btn btn--primary">
                                <i class="tio-add"></i>
                                <span class="text">{{\App\CPU\translate('Add Stock')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('SL')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Product Name')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Total stocks')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Total purchased stocks')}}</th>
                                <th class="text-center">{{\App\CPU\translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($pro as $k=>$p)

                             <?php 
                                //echo "<pre>"; print_r($p->stocks); die;
                                $total_stocks = \App\Model\ProductStock::where('product_id',$p['product_id'])->count();
                                $total_purchased_stocks = \App\Model\ProductStock::where('product_id',$p['product_id'])->where('is_purchased',1)->count();
                            ?>
                            <tr>
                                <th scope="row">{{$pro->firstItem()+$k}}</th>
                                <td class="text-center">
                                    <a href="javascript:void(0);" class="media align-items-center gap-2">
                                        <span class="media-body title-color hover-c1">
                                            {{\Illuminate\Support\Str::limit($p['product_name'],20)}}
                                        </span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{$total_stocks}}
                                </td>
                                <td class="text-center">
                                    {{$total_purchased_stocks}}
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a class="btn btn-outline--primary btn-sm square-btn"
                                            title="{{\App\CPU\translate('Edit')}}"
                                            href="{{route('admin.product.stocks.edit',[$p['product_id']])}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-danger btn-sm square-btn" href="javascript:"
                                            title="{{\App\CPU\translate('Delete')}}"
                                            onclick="form_alert('product-stock-{{$p['product_id']}}','Want to delete this item ?')">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.product.stocks.delete',[$p['product_id']])}}"
                                            method="post" id="product-stock-{{$p['product_id']}}">
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

<div class="modal fade" tabindex="-1" role="dialog" id="importModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{\App\CPU\translate('Import Excel')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                        aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="col-md-12 mt-2">
                <form class="product-form" action="{{route('admin.product.stocks.bulk-import')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card rest-part">
                        <div class="px-3 py-4 d-flex flex-wrap align-items-center gap-10 justify-content-center">
                            <h4 class="mb-0">{{\App\CPU\translate("Don`t_have_the_template_?")}}</h4>
                            <a href="{{asset('public/assets/product_stocks_bulk_format.xlsx')}}" download=""
                            class="btn-link text-capitalize fz-16 font-weight-medium">{{\App\CPU\translate('download_here')}}</a>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="row justify-content-center">
                                    <div class="col-auto">
                                        <div class="upload-file">
                                            <input type="file" name="products_file" accept=".xlsx, .xls" class="upload-file__input">
                                            <div class="upload-file__img_drag upload-file__img">
                                                <img src="{{asset('/public/assets/back-end/img/drag-upload-file.png')}}" alt="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-10 align-items-center justify-content-end">
                                <button type="reset" class="btn btn-secondary px-4" onclick="resetImg();">{{\App\CPU\translate('reset')}}</button>
                                <button type="submit" class="btn btn--primary px-4">{{\App\CPU\translate('Submit')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
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

        $('.upload-file__input').on('change', function() {
                $(this).siblings('.upload-file__img').find('img').attr({
                    'src': '{{asset('/public/assets/back-end/img/excel.png')}}',
                    'width': 80
                });
        });

        function resetImg() {
            // $('.upload-file__img img').attr({
            //     'src': '{{asset('/public/assets/back-end/img/drag-upload-file.png')}}',
            //     'width': 'auto'
            // });
            location.reload();
        }

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
                url: "{{route('admin.product.stocks.status-update')}}",
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

    </script>
@endpush
