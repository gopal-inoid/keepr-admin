@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Stocks Add'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
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


    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{asset('/assets/back-end/img/Stock_Management_Solid.svg')}}" alt="">
                {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('Stock')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.product.stocks.store') }}" method="POST" id="product_form">
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
                                        <select name="product_id" id="product_id" class="form-control new_stock_product">
                                            @if(!empty($product_options))
                                                @foreach($product_options as $prod_id => $val)
                                                    @if(!empty($val['product']))
                                                        {{!! $val['product'] !!}}
                                                    @endif
                                                @endforeach
                                            @endif
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
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="title-color">{{ \App\CPU\translate('Device ID') }}<span class="text-danger">*</span></label>
                                                <input type="text" name="device_id[]" maxlength="17" class="form-control" value="{{ old('device_id') }}" placeholder="{{ \App\CPU\translate('Device MAC ID') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="title-color">{{ \App\CPU\translate('UUID') }}<span class="text-danger">*</span></label>
                                                <input type="text" name="uuid[]" class="form-control uuid" style="text-transform: uppercase;" maxlength="36" id="uuid" value="{{ old('uuid') }}" placeholder="{{ \App\CPU\translate('UUID') }}" required>
                                                <span class="uuid_notice v_notice text-danger" id="uuid_notice"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="title-color">{{ \App\CPU\translate('major') }}<span class="text-danger">*</span></label>
                                                <input type="number" name="major[]" class="form-control " value="{{ old('major') }}" placeholder="{{ \App\CPU\translate('major') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="form-group">
                                                <label class="title-color">{{ \App\CPU\translate('minor') }}<span class="text-danger">*</span></label>
                                                <input type="number" name="minor[]" class="form-control " value="{{ old('minor') }}" placeholder="{{ \App\CPU\translate('minor') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <?php //echo "<pre>"; print_r($productcolors); die; ?>
                                            <div class="form-group">
                                                <label class="title-color">{{ \App\CPU\translate('Color') }}<span class="text-danger">*</span></label>
                                                <select name="colors[]" id="prodcolors-first" class="form-control prodcolors">
                                                    @if(!empty($product_options))
                                                        @foreach($product_options as $prod_id => $val)
                                                            @if(!empty($val['colors']))
                                                                @foreach($val['colors'] as $color)
                                                                    {{!!$color!!}}
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end gap-3 mt-3">
                        <button type="reset" class="btn btn-secondary">{{ \App\CPU\translate('reset') }}</button>
                        <button type="submit" class="btn btn--primary submit-btn">{{ \App\CPU\translate('Submit') }}</button>
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
       
                //     // UUID Fix Format Validation
                $(document).on("keydown",".uuid", function (e) {
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
                
                $(document).on("paste",".uuid", function (e) {
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
                $(".product-form").submit(function(e){
                    var isValidated=true;
                    $(".uuid").each(function(){
                        let uuid=$(this).val().trim();
                        if(!isValidUUID(uuid)){
                            $(this).next().html("");
                            $(this).next().html("Invalid uuid");
                            isValidated = false;
                        }
                        function isValidUUID(uuid) {
                            // Define the regular expression pattern
                            const pattern = /^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/;

                            // Use the test method to check if the UUID matches the pattern
                            return pattern.test(uuid);
                        }
                        if(uuid <= 0 || uuid.length < 36){
                            $(this).next().html("");
                             $(this).next().html("Invalid value");
                             $(this).next().val("");
                            isValidated = false;
                        }
                    });
                    if(!isValidated){
                    e.preventDefault();
                    window.scrollTo(0, 0);
                }
            });





    let new_stock_select=document.querySelector(".new_stock_product");
    let option=new_stock_select.querySelectorAll("OPTION");
    if(option.length==0){
    document.querySelector(".submit-btn").setAttribute("disabled","disabled");
    }

    // function formatMAC(e) {
    //     var r = /([a-f0-9]{2})([a-f0-9]{2})/i,
    //         str = e.target.value.replace(/[^a-f0-9]/ig, "");
    //     while (r.test(str)) {
    //         str = str.replace(r, '$1' + ':' + '$2');
    //     }
    //     e.target.value = str.slice(0, 17);
    // };

    // $(document).on('keyup','.macAddress',formatMAC);

        $(function() {
            $('.add-mac_id-btn').on('click',function(){

                var color_options = $('#prodcolors-first').html();

                $('#mac_id_device_field').append(
                    `<div class="row mac_id-individual">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="device_id[]" class="form-control" value="" placeholder="{{ \App\CPU\translate('Device ID') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" name="uuid[]" class="form-control uuid" style="text-transform: uppercase;" maxlength="36" id="uuid" value="{{ old('uuid') }}" placeholder="{{ \App\CPU\translate('UUID') }}" required>
                                <span class="uuid_notice v_notice text-danger" id="uuid_notice"></span>
                                </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <input type="number" name="major[]" class="form-control " value="{{ old('major') }}" placeholder="{{ \App\CPU\translate('major') }}" required>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <input type="number" name="minor[]" class="form-control " value="{{ old('minor') }}" placeholder="{{ \App\CPU\translate('minor') }}" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="colors[]" class="form-control prodcolors">
                                    `+color_options+`
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 form-group mac_id-add-main-btn">
                            <i class="tio-delete-outlined text-danger remove-mac_id-btn mt-0"></i>
                        </div>
                    </div>
                    `
                );
            });

            $(document).on('click','.remove-mac_id-btn',function(){
                $(this).closest('.mac_id-individual').remove();
            });

            $(document).on('change','#product_id',function(){
                var pro_id = $(this).val();
                $.get({
                    url: "{{route('admin.product.stocks.get-product-colors')}}",
                    dataType: 'json',
                    data:{product_id:pro_id},
                    success: function(data) {
                        if(data.colors != undefined){
                            $('.prodcolors').html(data.colors);
                        }
                    },
                });
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

                alert('a');

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
