@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Add'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
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
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{asset('/assets/back-end/img/Product_Solid.svg')}}" alt="">
                {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('Product')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.product.store') }}" method="POST"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data" id="product_form" autocomplete="off">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="lang_form" id="english-form">
                                        <div class="form-group">
                                            <label class="title-color" for="english_name">{{ \App\CPU\translate('Device Name') }}<span class="text-danger">*</span>
                                            </label>
                                            <input type="text" required name="name[]" autocomplete="off" id="english_name" class="form-control" placeholder="New Product">
                                            <span class="name_notice v_notice text-danger" id="name_notice"></span>
                                        </div>
                                        <input type="hidden" name="lang[]" value="english">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('product_code_sku') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" minlength="6" id="generate_number" autocomplete="off" name="code"
                                            class="form-control" value="{{ old('code') }}"
                                            placeholder="{{ \App\CPU\translate('code') }}" required>
                                            <span class="code_notice v_notice text-danger" id="code_notice"></span>
                                    </div>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Price') }}<span class="text-danger">*</span></label>
                                    <input type="number" min="0" step="0.01"
                                        placeholder="{{ \App\CPU\translate('Purchase price') }}"
                                        value="{{ old('purchase_price') }}" name="purchase_price" id="purchase_price"
                                        class="form-control" required autocomplete="off">
                                        <span class="price_notice v_notice text-danger" id="price_notice"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('RSSI') }}<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="{{ \App\CPU\translate('RSSI') }}"
                                        value="{{ old('rssi') }}" name="rssi" id="rssi"
                                        class="form-control" required autocomplete="off">
                                        <span class="rssi_notice v_notice text-danger" id="rssi_notice"></span>
                                </div>
                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('UUID') }}<span class="text-danger">*</span></label>
                                    <input type="text" placeholder="{{ \App\CPU\translate('UUID') }}"
                                        value="{{ old('uuid') }}" name="uuid" id="uuid" maxlength="36" style="text-transform:uppercase;"
                                        class="form-control" required autocomplete="off">
                                        <span class="uuid_notice v_notice text-danger" id="uuid_notice"></span>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="product-desc">{{ \App\CPU\translate('description') }}</label>
                                        <textarea name="description" id="product-desc" class="textarea editor-textarea" autocomplete="timezone_offset_get">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
																	<div class="row spec-individual d-none">
																		<div class="col-md-4 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Key') }}</label>
																		</div>
																		<div class="col-md-6 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Value') }}</label>
																		</div>
																	</div>
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
																	<div class="row faq-individual d-none">
																		<div class="col-md-4 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Question') }}</label>
																		</div>
																		<div class="col-md-6 form-group mb-0">
																				<label class="title-color">{{ \App\CPU\translate('Answer') }}</label>
																		</div>
																	</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Product colors') }}<span class="text-danger">*</span></h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-12 form-group" id="parent-colors-div">
                                    <div class="row colors-individual">
                                        <div class="col-md-6 form-group">
                                            <select name="colors[]" class="form-control color-select" multiple required>
                                                @if(!empty($colors))
                                                    @foreach($colors as $col)
                                                        <option value="{{$col['id']}}">{{$col['name']}}</option>
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
                                <div class="col-md-8 form-group">
                                    <div class="mb-2">
                                        <label class="title-color">{{ \App\CPU\translate('Upload product images') }}<span class="text-danger">*</span></label>
                                        <span class="text-info">* ( {{ \App\CPU\translate('ratio') }} 1:1 )</span>
                                    </div>
                                    <div class="p-2 border border-dashed">
                                        <div class="row" id="coba"></div>
                                    </div>

                                </div>

                                <div class="col-md-4 form-group">
                                    <div class="mb-2">
                                        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('Upload thumbnail') }}<span class="text-danger">*</span></label>
                                        <span class="text-info">* ( {{ \App\CPU\translate('ratio') }} 1:1 )</span>
                                    </div>
                                    <div>
                                        <div class="row" id="thumbnail"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row justify-content-end gap-3 mt-3">
                        <button type="reset" class="btn btn-secondary">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/assets/back-end') }}/js/tags-input.min.js"></script>
    <script src="{{ asset('public/assets/back-end/js/spartan-multi-image-picker.js') }}"></script>
    <script>
        $(function() {

                // UUID Fix Format Validation

                $("#uuid").on("keydown", function (e) {
                         let keycode=e.keyCode|| e.which;
                         let ctrlKey = e.ctrlKey || e.metaKey;
                         let value=$(this).val().trim();
                            uuidinputFormat(value,this);
                            function uuidinputFormat(value,elm){
                                if(value.length<=36){
                                    if(value.length==8||value.length==13||value.length==18||value.length==23){
                                            elm.value += '-';
                                    } 
                                    const lastChar = value.charAt(value.length - 1);
                                    if (lastChar === '-') {
                                    value = value.substring(0, value.length - 1);
                                    elm.value=value;
                                    }
                                }     
                            } 
                    });
                
                $("#uuid").on("paste", function (e) {
                     let elm = $(this);
                     setTimeout(function(){
                        let value=$(elm).val().trim();
                        if(!isValidUUID(value)){
                            $(elm).val(convertToUUID(value));
                        }else{
                            $(elm).val(value);
                        }
                    },10);
                    function convertToUUID(value) {
                    // Remove any unwanted characters (e.g., spaces or dashes)
                    const cleanedValue = value.replace(/[^0-9A-Fa-f]/g, '');
                    // Ensure the cleaned value has the correct length
                    const formattedValue = cleanedValue.slice(0, 8) + '-' +
                                            cleanedValue.slice(8, 12) + '-' +
                                            cleanedValue.slice(12, 16) + '-' +
                                            cleanedValue.slice(16, 20) + '-' +
                                            cleanedValue.slice(20, 32);

                    return formattedValue;
                    } 
                    function isValidUUID(value) {
                            const pattern = /^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/;
                            return pattern.test(value);
                    }
                });
                
            // Price, Rssi, Uuid is made mandatory non-zero & non negative 
            $(".product-form").submit(function(e){
                var isValidated=true;
                $(".v_notice").each(function(){
                    $(this).html("");
                });

                let deviceName=$("#english_name").val();
                let val1 = $.trim(deviceName);
                if(val1.length<=0){ 
                    $(".name_notice").html("");
                    $(".name_notice").html("Empty field alert");
                    $("#english_name").val("");
                    isValidated = false;
                }

                let code=$("#generate_number").val();
                let val2 = $.trim(code);
                if(val2.length<=0){        
                    $(".code_notice").html("");
                    $(".code_notice").html("Empty field alert");
                    $("#generate_number").val("");
                    isValidated = false;
                }

                let price=$("#purchase_price").val();
                if(price <= 0 || price == ""){ 
                    $(".price_notice").html("");
                    $(".price_notice").html("Invalid value");
                    $("#purchase_price").val("");
                    isValidated = false;
                }

                let rssi=$("#rssi").val().trim();
                if(rssi.length <=0){ 
                    $(".rssi_notice").html("");
                    $(".rssi_notice").html("Invalid value");
                    $("#rssi").val("");
                    isValidated = false;
                    }

                let uuid=$("#uuid").val().trim();
                let array=uuid.split('-');
                const sum = array.reduce((accumulator, currentValue) => {
                    return accumulator + currentValue;
                }, 0);
                if(!isValidUUID(uuid)){
                    $(".uuid_notice").html("");
                    $(".uuid_notice").html("Invalid uuid");
                    isValidated = false;
                }
                function isValidUUID(uuid) {
                    // Define the regular expression pattern
                    const pattern = /^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/;

                    // Use the test method to check if the UUID matches the pattern
                    return pattern.test(uuid);
                }
                if(uuid <= 0 || uuid.length < 36||sum==0){
                        $(".uuid_notice").html("");
                        $(".uuid_notice").html("Invalid value");
                        $("#uuid").val("");
                        isValidated = false;
                }
               

                if(!isValidated){
                e.preventDefault();
                window.scrollTo(0, 0);
                }
            });
            // Front-End validation for Price, Rssi & UUid is right above

            $('.color-select').select2({
                placeholder:"Select colors"
            });

            $('.add-product-spec-btn').on('click',function(){
                if($(".spec-individual").length == 1){
                    $(".spec-individual").eq(0).removeClass('d-none');
                }
                $('#parent-spec-div').append(
                        `<div class=" spec-individual d-flex">
                                <div class="col-md-4 p-xs-0 p-sm-0 form-group">
                                        <input type="text" value="" name="spec[key][]" class="form-control" placeholder="{{ \App\CPU\translate('Key') }}">
                                </div>
                                <div class="col-md-6 p-xs-0 p-sm-0 form-group">
                                        <input type="text" value="" name="spec[value][]" class="form-control" placeholder="{{ \App\CPU\translate('Value') }}">
                                </div>
                                <div class="col-md-2 p-xs-0 p-sm-0 form-group spec-add-main-btn">
                                        <i class="tio-delete-outlined text-danger remove-product-spec-btn"></i>
                                </div>
                        </div>`
                );
			});

            // $(document).on('click','.add-product-colors-btn',function(){
            //     if($(".colors-individual").length == 1){
            //         $(".colors-individual").eq(0).removeClass('d-none');
            //     }
            //     $('#parent-colors-div').append(
            //         `<div class="row colors-individual">
            //                 <div class="col-md-4 form-group">
            //                     <select name="colors[key][]" class="form-control select2" multiple>
            //                         @if(!empty($color))
            //                             @foreach($color as $col)
            //                                 <option value="{{$col['code']}}">{{$col['name']}}</option>
            //                             @endforeach
            //                         @endif
            //                     </select>
            //                 </div>
            //                 <div class="col-md-2 form-group colors-add-main-btn">
            //                     <i class="tio-delete-outlined text-danger remove-product-colors-btn"></i>
            //                 </div>
            //         </div>`
            //     );
			// });

            $(document).on('click','.remove-product-spec-btn',function(){
                $(this).closest('.spec-individual').remove();
                if($(".spec-individual").length == 1){
                    $(".spec-individual").eq(0).addClass('d-none');
                }
            });

            // $(document).on('click','.remove-product-colors-btn',function(){
            //     $(this).closest('.colors-individual').remove();
            //     if($(".colors-individual").length == 1){
            //         $(".colors-individual").eq(0).addClass('d-none');
            //     }
            // });

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


          
            $("#coba").spartanMultiImagePicker({
                fieldName: 'images[]',
                maxCount: 10,
                rowHeight: 'auto',
                groupClassName: 'col-6',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {
                },
                onRenderedPreview: function(index) {
                },
                onRemoveRow: function(index) {
                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                    '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#thumbnail").spartanMultiImagePicker({
                fieldName: 'image',
                maxCount: 1,
                rowHeight: 'auto',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
                    width: '100%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                    '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });

            $("#meta_img").spartanMultiImagePicker({
                fieldName: 'meta_image',
                maxCount: 1,
                rowHeight: '280px',
                groupClassName: 'col-12',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{ asset('public/assets/back-end/img/400x400/img2.jpg') }}',
                    width: '90%',
                },
                dropFileLabel: "Drop Here",
                onAddRow: function(index, file) {

                },
                onRenderedPreview: function(index) {

                },
                onRemoveRow: function(index) {

                },
                onExtensionErr: function(index, file) {
                    toastr.error(
                    '{{ \App\CPU\translate('Please only input png or jpg type file') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function(index, file) {
                    toastr.error('{{ \App\CPU\translate('File size too big') }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });





        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileUpload").change(function() {
            readURL(this);
        });


        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });
    </script>

    <script>
        function getRequest(route, id, type) {
            $.get({
                url: route,
                dataType: 'json',
                success: function(data) {
                    if (type == 'select') {
                        $('#' + id).empty().append(data.select_tag);
                    }
                },
            });
        }

        $('input[name="colors_active"]').on('change', function() {
            if (!$('input[name="colors_active"]').is(':checked')) {
                $('#colors-selector').prop('disabled', true);
            } else {
                $('#colors-selector').prop('disabled', false);
            }
        });

        $('#choice_attributes').on('change', function() {
            $('#customer_choice_options').html(null);
            $.each($("#choice_attributes option:selected"), function() {
                //console.log($(this).val());
                add_more_customer_choice_option($(this).val(), $(this).text());
            });
        });

        function add_more_customer_choice_option(i, name) {
            let n = name.split(' ').join('');
            $('#customer_choice_options').append(
                '<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i +
                '"><input type="text" class="form-control" name="choice[]" value="' + n +
                '" placeholder="{{ trans('Choice Title') }}" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' +
                i +
                '[]" placeholder="{{ trans('Enter choice values') }}" data-role="tagsinput" onchange="update_sku()"></div></div>'
                );

            $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
        }


        $('#colors-selector').on('change', function() {
            update_sku();
        });

        $('input[name="unit_price"]').on('keyup', function() {
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
                url: '{{ route('admin.product.sku-combination') }}',
                data: $('#product_form').serialize(),
                success: function(data) {
                    $('#sku_combination').html(data.view);
                    if (data.length > 1) {
                        $('#quantity').hide();
                    } else {
                        $('#quantity').show();
                    }
                }
            });
        }

        $(document).ready(function() {
            // color select select2
            $('.color-var-select').select2({
                templateResult: colorCodeSelect,
                templateSelection: colorCodeSelect,
                escapeMarkup: function(m) {
                    return m;
                }
            });

            function colorCodeSelect(state) {
                var colorCode = $(state.element).val();
                if (!colorCode) return state.text;
                return "<span class='color-preview' style='background-color:" + colorCode + ";'></span>" + state
                    .text;
            }
        });
    </script>

    <script>
        function check() {
            Swal.fire({
                title: '{{ \App\CPU\translate('Are you sure') }}?',
                text: '{{ \App\CPU\translate('Want to add this product') }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
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
                    url: '{{ route('admin.product.store') }}',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        //console.log(data.errors);
                        // return false;
                        // if (data.errors) {
                        //     for (var i = 0; i < data.errors.length; i++) {
                        //         toastr.error(data.errors[i].message, {
                        //             CloseButton: true,
                        //             ProgressBar: true
                        //         });
                        //     }
                        // } else {
                            toastr.success(
                            '{{ \App\CPU\translate('product added successfully') }}!', {
                                CloseButton: true,
                                ProgressBar: true
                            });
                            $('#product_form').submit();
                        //}
                    }
                });
            })
        };
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
                $('#digital_product_type').val($('#digital_product_type option:first').val());
                $('#digital_file_ready').val('');
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

    {{-- ck editor --}}
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script>
    <script>
        $('.textarea').ckeditor({
            contentsLangDirection: '{{ Session::get('direction') }}',
        });
    </script>

    {{-- ck editor --}}
@endpush
