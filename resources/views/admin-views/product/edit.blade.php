@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Edit'))

@push('css_or_js')
    <link href="{{asset('public/assets/back-end/css/tags-input.min.css')}}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css')}}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .add-product-spec-btn,.remove-product-spec-btn,.add-product-faq-btn,
        .remove-product-faq-btn,.add-product-colors-btn,.remove-product-colors-btn{
            font-size: 25px;
            cursor: pointer;
        }
        .spec-add-main-btn, .faq-add-main-btn,.colors-add-main-btn{
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
                {{\App\CPU\translate('Product')}} {{\App\CPU\translate('Edit')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{route('admin.product.update',$product->id)}}" method="post"
                      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};"
                      enctype="multipart/form-data"
                      id="product_form">
                    @csrf

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="lang_form" id="english-form">
                                        <div class="form-group">
                                            <label class="title-color" for="english_name">{{ \App\CPU\translate('Device Name') }}
                                            </label>
                                            <input type="text" required name="name[]" id="english_name" value="{{$product['name']}}" class="form-control" placeholder="New Product">
                                        </div>
                                        <input type="hidden" name="lang[]" value="english">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="title-color"
                                               for="exampleFormControlInput1">{{ \App\CPU\translate('product_code_sku') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" id="generate_number" name="code"
                                               class="form-control"  value="{{ $product->code  }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Price') }}</label>
                                    <input type="number" min="0" step="0.01"
                                        placeholder="{{ \App\CPU\translate('Purchase price') }}"
                                        value="{{ $product->purchase_price }}" name="purchase_price"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('RSSI') }}</label>
                                    <input type="text" placeholder="{{ \App\CPU\translate('RSSI') }}"
                                        value="{{ $product->rssi }}" name="rssi"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('UUID') }}</label>
                                    <input type="text" placeholder="{{ \App\CPU\translate('UUID') }}"
                                        value="{{ $product->uuid }}" name="uuid" id="uuid" maxlength="36" style="text-transform:uppercase;"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="product-desc">{{ \App\CPU\translate('description') }}</label>
                                        <textarea name="description" id="product-desc" class="textarea editor-textarea">{{ $product['details'] }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $specifications = $faqs = $productColors = [];
                        if(!empty($product['colors'])){
                            $productColors = explode(",", $product['colors']);
                        }
                        if(!empty($product['specification'])){
                            $specifications =  json_decode($product['specification'],true);
                        }
                        if(!empty($product['faq'])){
                            $faqs = json_decode($product['faq'],true);
                        }
                    ?>
                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Product Specifications') }}</h4>
														<div class="spec-add-main-btn">
																<label class="title-color">&nbsp;</label>
																<i class="tio-add-circle-outlined text-success add-product-spec-btn mt-3"></i>
														</div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-12 form-group" id="parent-spec-div">
																	<div class="row spec-individual <?= empty($specifications) ? 'd-none' : '';?>">
																		<div class="col-md-4 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Key') }}</label>
																		</div>
																		<div class="col-md-6 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Value') }}</label>
																		</div>
																	</div>
                                    <?php 
                                        if(!empty($specifications)){
                                            foreach($specifications as $k => $spec){ ?>
                                                <div class="row spec-individual">
                                                    <div class="col-md-4 form-group">
																											<input type="text" value="{{$spec['key']}}" name="spec[key][]" class="form-control" placeholder="{{ \App\CPU\translate('Key') }}">
                                                    </div>
                                                    <div class="col-md-6 form-group">
																											<input type="text" value="{{$spec['value']}}" name="spec[value][]" class="form-control" placeholder="{{ \App\CPU\translate('Value') }}">
                                                    </div>
																										<div class="col-md-2 form-group spec-add-main-btn">
																												<i class="tio-delete-outlined text-danger remove-product-spec-btn"></i>
																										</div>
                                                </div>
                                    <?php  } } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Product FAQ') }}</h4>
														<div class="faq-add-main-btn">
																<label class="title-color">&nbsp;</label>
																<i class="tio-add-circle-outlined text-success add-product-faq-btn mt-3"></i>
														</div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-12 form-group" id="parent-faq-div">
																	<div class="row faq-individual <?= empty($faqs) ? 'd-none' : '';?>">
																		<div class="col-md-4 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Question') }}</label>
																		</div>
																		<div class="col-md-6 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Answer') }}</label>
																		</div>
																	</div>
                                    <?php 
                                        if(!empty($faqs)){
                                            foreach($faqs as $k => $faq){ ?>
                                                <div class="row faq-individual">
                                                    <div class="col-md-4 form-group">
																											<input type="text" value="{{$faq['question']}}" name="faq[question][]" class="form-control" placeholder="{{ \App\CPU\translate('Question') }}">
                                                    </div>
                                                    <div class="col-md-6 form-group">
																											<input type="text" value="{{$faq['answer']}}" name="faq[answer][]" class="form-control" placeholder="{{ \App\CPU\translate('Answer') }}">
                                                    </div>
																										<div class="col-md-2 form-group faq-add-main-btn">
																												<i class="tio-delete-outlined text-danger remove-product-faq-btn"></i>
																										</div>
                                                </div>
                                    <?php  } } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Product colors') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-12 form-group" id="parent-colors-div">
                                    <div class="row colors-individual">
                                        <div class="col-md-6 form-group">
                                            <select name="colors[]" class="form-control color-select" multiple>
                                                @if(!empty($colors))
                                                    @foreach($colors as $col)
                                                        <option value="{{$col['id']}}" <?= in_array($col['id'], $productColors) ? "selected='selected'" : "" ?>>{{$col['name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part">
                        <div class="card-body">
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label class="title-color">{{\App\CPU\translate('Upload product images')}}</label>
                                        <span class="text-info"><span class="text-danger">*</span> ( {{\App\CPU\translate('ratio')}} 1:1 )</span>
                                    </div>
                                    <div class="p-2 border border-dashed">
                                        <div class="row gy-3" id="coba">
                                                @foreach (json_decode($product->images) as $key => $photo)
                                                    <div class="col-sm-6">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <img class="w-100" height="auto"
                                                                    src="{{asset("/product/$photo")}}"
                                                                    onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                                    alt="Product image">
                                                                <a href="{{route('admin.product.remove-image',['id'=>$product['id'],'name'=>$photo])}}"
                                                                class="btn btn-danger btn-block">{{\App\CPU\translate('Remove')}}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="mb-2">
                                        <label for="name" class="title-color">{{\App\CPU\translate('Upload thumbnail')}}</label>
                                        <span class="text-info"><span class="text-danger">*</span> ( {{\App\CPU\translate('ratio')}} 1:1 )</span>
                                    </div>

                                    <div class="row gy-3" id="thumbnail">
                                        <div class="col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img class="w-100" height="auto"
                                                         onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                         src="{{asset("/product/thumbnail")}}/{{$product['thumbnail']}}"
                                                         alt="Product image">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end pt-3">
                                @if($product->request_status == 2)
                                    <button type="button" onclick="check()" class="btn btn--primary">{{\App\CPU\translate('Update & Publish')}}</button>
                                @else
                                    <button type="submit" class="btn btn--primary">{{\App\CPU\translate('Update')}}</button>
                                @endif
                            </div>
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
                // UUID Fix Format Validation
                 $("#uuid").on("input", function () {
                    let value=$(this).val().trim();
                    uuidinputFormat(value,this);
                    function uuidinputFormat(value,elm){
                        if(value.length<=36){
                            if(value.length==8||value.length==13||value.length==18||value.length==23){
                                    elm.value += '-';
                            }  
                        }
                        const lastChar = value.charAt(value.length - 1);
                            if (lastChar === '-') {
                            value = value.substring(0, value.length - 1);
                            elm.value=value;
                        }
                    }
                });
              
                $("#uuid").on("paste", function () {
                     let elm = $(this);
                     setTimeout(function(){
                        let value=$(elm).val().trim();
                        $(elm).val(uuidpestFormat(value));
                    },10);
                    function uuidpestFormat(value){
                    if(value.length<=32){
                        if (value.length >= 8) {
                        value = value.substring(0, 8) + '-' + value.substring(8);
                        }
                        if (value.length >= 13) {
                        value = value.substring(0, 13) + '-' + value.substring(13);
                        }
                        if (value.length >= 18) {
                        value = value.substring(0, 18) + '-' + value.substring(18);
                        }
                        if (value.length >= 23) {
                        value = value.substring(0, 23) + '-' + value.substring(23);
                        }
                        return value;
                    }
                } 
                });
               
        imageCount = 0;
        @if(!empty($product->images))
        var imageCount = {{10-count(json_decode($product->images))}};
        @endif
        var thumbnail = '{{\App\CPU\ProductManager::product_image_path('thumbnail').'/'.$product->thumbnail??asset('public/assets/back-end/img/400x400/img2.jpg')}}';
        $(function () {

            $('.add-product-spec-btn').on('click',function(){
                    if($(".spec-individual").length == 1){
                        $(".spec-individual").eq(0).removeClass('d-none');
                    }
                    $('#parent-spec-div').append(
                            `<div class="row spec-individual">
                                    <div class="col-md-4 form-group">
                                            <input type="text" value="" name="spec[key][]" class="form-control" placeholder="{{ \App\CPU\translate('Key') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                            <input type="text" value="" name="spec[value][]" class="form-control" placeholder="{{ \App\CPU\translate('Value') }}">
                                    </div>
                                    <div class="col-md-2 form-group spec-add-main-btn">
                                            <i class="tio-delete-outlined text-danger remove-product-spec-btn"></i>
                                    </div>
                            </div>`
                    );
            });

            $(document).on('click','.remove-product-spec-btn',function(){
                $(this).closest('.spec-individual').remove();
                if($(".spec-individual").length == 1){
                    $(".spec-individual").eq(0).addClass('d-none');
                }
            });

            $('.add-product-faq-btn').on('click',function(){
							if($(".faq-individual").length == 1){
								$(".faq-individual").eq(0).removeClass('d-none');
							}
                $('#parent-faq-div').append(
                    `<div class="row faq-individual">
                        <div class="col-md-4 form-group">
                            <input type="text" value="" name="faq[question][]" class="form-control" placeholder="{{ \App\CPU\translate('Question') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" value="" name="faq[answer][]" class="form-control" placeholder="{{ \App\CPU\translate('Answer') }}">
                        </div>
                        <div class="col-md-2 form-group faq-add-main-btn">
                            <i class="tio-delete-outlined text-danger remove-product-faq-btn"></i>
                        </div>
                    </div>`
                );
            });

            $(document).on('click','.remove-product-faq-btn',function(){
                $(this).closest('.faq-individual').remove();
								if($(".faq-individual").length == 1){
									$(".faq-individual").eq(0).addClass('d-none');
								}
            });

            if (imageCount > 0) {
                $("#coba").spartanMultiImagePicker({
                    fieldName: 'images[]',
                    maxCount: imageCount,
                    rowHeight: 'auto',
                    groupClassName: 'col-6',
                    maxFileSize: '',
                    placeholderImage: {
                        image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                        width: '100%',
                    },
                    dropFileLabel: "Drop Here",
                    onAddRow: function (index, file) {

                    },
                    onRenderedPreview: function (index) {

                    },
                    onRemoveRow: function (index) {

                    },
                    onExtensionErr: function (index, file) {
                        toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    },
                    onSizeErr: function (index, file) {
                        toastr.error('{{\App\CPU\translate('File size too big')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                });
            }

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/back-end/img/400x400/img2.jpg')}}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('Please only input png or jpg type file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{\App\CPU\translate('File size too big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function () {
            readURL(this);
        });

        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            width: 'resolve'
        });

				$('.color-select').select2({
						placeholder:"Select colors"
				});
    </script>

    <script>
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
        function check() {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var formData = new FormData(document.getElementById('product_form'));
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.product.update',$product->id)}}',
                data: formData,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('product updated successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        $('#product_form').submit();
                    }
                }
            });
        };
    </script>

    <script>
        update_qty();

        function update_qty() {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            if (qty_elements.length > 0) {

                $('input[name="current_stock"]').attr("readonly", true);
                $('input[name="current_stock"]').val(total_qty);
            } else {
                $('input[name="current_stock"]').attr("readonly", false);
            }
        }

        $('input[name^="qty_"]').on('keyup', function () {
            var total_qty = 0;
            var qty_elements = $('input[name^="qty_"]');
            for (var i = 0; i < qty_elements.length; i++) {
                total_qty += parseInt(qty_elements.eq(i).val());
            }
            $('input[name="current_stock"]').val(total_qty);
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
