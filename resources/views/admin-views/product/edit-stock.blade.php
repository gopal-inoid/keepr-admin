@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Stocks Edit'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .add-mac_id-btn,.remove-mac_id-btn{
            font-size: 25px;
            cursor: pointer;
        }
        .mac_id-add-main-btn{
            align-items: center;
            display: flex;
            flex-direction: row;
        }
        
    </style>
@endpush

@section('content')
    <!-- Page Heading -->
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
                {{\App\CPU\translate('Product')}} {{\App\CPU\translate('Stocks')}} {{\App\CPU\translate('Edit')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.product.stocks.update',$id)}}" method="post" id="product_form">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('Products') }}
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select name="product_id" class="form-control" disabled>
																					<option selected="selected" value="{{$product['id']}}">{{$product['name']}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color">&nbsp;</label>
                                        <button type="button" class="tio-add-circle-outlined btn text-success add-mac_id-btn mt-4"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-12" id="mac_id_device_field">
                                        <div class="row mac_id-individual">
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="title-color">{{ \App\CPU\translate('Device ID') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="title-color">{{ \App\CPU\translate('UUID') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group mb-0">
                                                <label class="title-color">{{ \App\CPU\translate('major') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group mb-0">
                                                <label class="title-color">{{ \App\CPU\translate('minor') }}</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group mb-0">
                                                    <label class="title-color">{{ \App\CPU\translate('Color') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        $product_stock_cnt = !empty($product_stock) ? count($product_stock) : 0;
                                        if(!empty($product_stock)){
                                            foreach($product_stock as $k => $stocks){ ?>
                                            <div class="row mac_id-individual">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="device_id[{{$k}}]" class="form-control macAddress" value="{{ $stocks->mac_id }}" placeholder="{{ \App\CPU\translate('Device ID') }}">
                                                    </div>
                                                </div>
												<div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="uuid[{{$k}}]" class="form-control " value="{{ $stocks->uuid }}" placeholder="{{ \App\CPU\translate('UUID') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <input type="number" name="major[{{$k}}]" class="form-control " value="{{ $stocks->major }}" placeholder="{{ \App\CPU\translate('major') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="form-group">
                                                        <input type="number" name="minor[{{$k}}]" class="form-control " value="{{ $stocks->minor }}" placeholder="{{ \App\CPU\translate('minor') }}" required>
                                                    </div>
                                                </div>										
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <select name="colors[]" class="form-control">
                                                            @if(!empty($product['colors']) && !empty($colors))
                                                                @php
                                                                $productColors = explode(",", $product['colors']);
                                                                @endphp
                                                                @foreach($colors as $col)
                                                                    @if(in_array($col['id'], $productColors))
                                                                        <option value="{{$col['id']}}" {{ $stocks->color == $col['id'] ? "selected='selected'" : "" }}>{{$col['name']}}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                                @if(!empty($stocks->is_purchased))
                                                    <div class="col-md-2 form-group">
                                                        <span class="badge text-success fz-12 px-0 mt-2">Purchased</span>
                                                    </div>
                                                @else
                                                    <div class="col-md-2 form-group mac_id-add-main-btn">
                                                        <i class="tio-delete-outlined text-danger remove-mac_id-btn"></i>
                                                    </div>
                                                @endif
                                            </div>
                                    <?php  } } ?>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="row float-right mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{asset('public/assets/back-end')}}/js/tags-input.min.js"></script>
    <script src="{{asset('public/assets/back-end/js/spartan-multi-image-picker.js')}}"></script>
    <script>

    function formatMAC(e) {
        var r = /([a-f0-9]{2})([a-f0-9]{2})/i,
            str = e.target.value.replace(/[^a-f0-9]/ig, "");
        
        while (r.test(str)) {
            str = str.replace(r, '$1' + ':' + '$2');
        }

        e.target.value = str.slice(0, 17);
    };

    $(document).on('keyup','.macAddress',formatMAC);

        var cnt = (parseInt("{{$product_stock_cnt}}") - 1);

        $('.add-mac_id-btn').on('click',function(){
					if($(".mac_id-individual").length == 1){
							$(".mac_id-individual").eq(0).removeClass('d-none');
					}
            $('#mac_id_device_field').append(
                `<div class="row mac_id-individual">
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="device_id[`+(cnt+1)+`]" class="form-control" value="" placeholder="{{ \App\CPU\translate('Device ID') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" name="uuid[`+(cnt+1)+`]" class="form-control " value="" placeholder="{{ \App\CPU\translate('UUID') }}" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <input type="number" name="major[`+(cnt+1)+`]" class="form-control " value="" placeholder="{{ \App\CPU\translate('major') }}" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <input type="number" name="minor[`+(cnt+1)+`]" class="form-control " value="" placeholder="{{ \App\CPU\translate('minor') }}" required>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <select name="colors[]" class="form-control">
                                @if(!empty($product['colors']) && !empty($colors))
                                    @php
                                    $productColors = explode(",", $product['colors']);
                                    @endphp
                                    @foreach($colors as $col)
                                        @if(in_array($col['id'], $productColors))
                                            <option value="{{$col['id']}}">{{$col['name']}}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 form-group mac_id-add-main-btn">
                        <i class="tio-delete-outlined text-danger remove-mac_id-btn mt-0"></i>
                    </div>
                </div>
                `
            );
            cnt++;
        });

        $(document).on('click','.remove-mac_id-btn',function(){
            $(this).closest('.mac_id-individual').remove();
						if($(".mac_id-individual").length == 1){
								$(".mac_id-individual").eq(0).addClass('d-none');
						}
        });

        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function () {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function () {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function () {
                //console.log($(this).val());
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append('<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i + '"><input type="text" class="form-control" name="choice[]" value="' + n + '" placeholder="{{\App\CPU\translate('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' + i + '[]" placeholder="{{\App\CPU\translate('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>');
            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }

        setTimeout(function () {
            $('.call-update-sku').on('change', function () {
                update_sku();
            });
        }, 2000)

        $('#colors-selector').on('change', function () {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function () {
            let product_type = $('#product_type').val();
            if(product_type === 'physical') {
                update_sku();
            }
        });

        function update_sku() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '{{route('admin.product.sku-combination')}}',
                data: $('#product_form').serialize(),
                success: function (data) {
                    $('#sku_combination').html(data.view);
                    update_qty();
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category_id").val();
                let sub_category = $("#sub-category-select").attr("data-id");
                let sub_sub_category = $("#sub-sub-category-select").attr("data-id");
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + category + '&sub_category=' + sub_category, 'sub-category-select', 'select');
                getRequest('{{url('/')}}/admin/product/get-categories?parent_id=' + sub_category + '&sub_category=' + sub_sub_category, 'sub-sub-category-select', 'select');
            }, 100)
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state.text;
            }
        });
    </script>
 
    <script>
        $(document).ready(function(){
            product_type();
            digital_product_type();

            $('#product_type').change(function(){
                product_type();
            });

            $('#digital_product_type').change(function(){
                digital_product_type();
            });
        });

        function product_type(){
            let product_type = $('#product_type').val();

            if(product_type === 'physical'){
                $('#digital_product_type_show').hide();
                $('#digital_file_ready_show').hide();
                $('.physical_product_show').show();
                $("#digital_product_type").val($("#digital_product_type option:first").val());
                $("#digital_file_ready").val('');
            }else if(product_type === 'digital'){
                $('#digital_product_type_show').show();
                $('.physical_product_show').hide();

            }
        }

        function digital_product_type(){
            let digital_product_type = $('#digital_product_type').val();
            if (digital_product_type === 'ready_product') {
                $('#digital_file_ready_show').show();
            } else if (digital_product_type === 'ready_after_sell') {
                $('#digital_file_ready_show').hide();
                $("#digital_file_ready").val('');
            }
        }
    </script>

    {{--ck editor--}}
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{asset('/')}}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection : '{{Session::get('direction')}}',
        });
    </script>
    {{--ck editor--}}
@endpush
