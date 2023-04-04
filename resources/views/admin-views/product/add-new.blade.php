@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Product Add'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .add-product-faq-btn,.remove-product-faq-btn{
            font-size: 25px;
            cursor: pointer;
        }
        .faq-add-main-btn{
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
                <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
                {{\App\CPU\translate('Add')}} {{\App\CPU\translate('New')}} {{\App\CPU\translate('Product')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.product.store') }}" method="POST"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data" id="product_form">
                    @csrf
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="lang_form" id="english-form">
                                        <div class="form-group">
                                            <label class="title-color" for="english_name">{{ \App\CPU\translate('Device Name') }}
                                            </label>
                                            <input type="text" required name="name[]" id="english_name" class="form-control" placeholder="New Product">
                                        </div>
                                        <input type="hidden" name="lang[]" value="english">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="exampleFormControlInput1">{{ \App\CPU\translate('product_code_sku') }}
                                            <span class="text-danger">*</span></label>
                                        <input type="text" minlength="6" id="generate_number" name="code"
                                            class="form-control" value="{{ old('code') }}"
                                            placeholder="{{ \App\CPU\translate('code') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Price') }}</label>
                                    <input type="number" min="0" step="0.01"
                                        placeholder="{{ \App\CPU\translate('Purchase price') }}"
                                        value="{{ old('purchase_price') }}" name="purchase_price"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-2 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('RSSI') }}</label>
                                    <input type="text" placeholder="{{ \App\CPU\translate('RSSI') }}"
                                        value="{{ old('rssi') }}" name="rssi"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="product-desc">{{ \App\CPU\translate('description') }}</label>
                                        <textarea name="description" id="product-desc" class="textarea editor-textarea">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Specification') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                
                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Size') }}</label>
                                    <input type="text" value="{{ old('size') }}" name="specification[size]" class="form-control" placeholder="31.1 * 30 * 5.5mm">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Weight') }}</label>
                                    <input type="text" value="{{ old('weight') }}" name="specification[weight]" class="form-control" placeholder="4.8g (battery included)">
                                </div>

                                <div class="col-md-4 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Bluetooth version') }}</label>
                                    <input type="text" value="{{ old('bluetooth_version') }}" name="specification[bluetooth_version]" class="form-control" placeholder="LE 4.0 / 4.2">
                                </div>

                                <div class="col-md-12 form-group">
                                    <label class="title-color">{{ \App\CPU\translate('Material') }}</label>
                                    <textarea name="specification[material]" class="form-control" placeholder="{{ \App\CPU\translate('Material') }}">{{ old('material') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card mt-2 rest-part physical_product_show">
                        <div class="card-header">
                            <h4 class="mb-0">{{ \App\CPU\translate('Product FAQ') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-12 form-group" id="parent-faq-div">
                                    <div class="row">
                                        <div class="col-md-4 form-group">
                                            <label class="title-color">{{ \App\CPU\translate('Question') }}</label>
                                            <input type="text" value="{{ old('question') }}" name="faq[question][]" class="form-control" placeholder="{{ \App\CPU\translate('Question') }}">
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label class="title-color">{{ \App\CPU\translate('Answer') }}</label>
                                            <input type="text" value="{{ old('answer') }}" name="faq[answer][]" class="form-control" placeholder="{{ \App\CPU\translate('Answer') }}">
                                        </div>
                                        <div class="col-md-2 form-group faq-add-main-btn">
                                            <label class="title-color">&nbsp;</label>
                                            <i class="tio-add-circle-outlined text-success add-product-faq-btn mt-3"></i>
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
                                        <label class="title-color">{{ \App\CPU\translate('Upload product images') }}</label>
                                        <span class="text-info">* ( {{ \App\CPU\translate('ratio') }} 1:1 )</span>
                                    </div>
                                    <div class="p-2 border border-dashed">
                                        <div class="row" id="coba"></div>
                                    </div>

                                </div>

                                <div class="col-md-4 form-group">
                                    <div class="mb-2">
                                        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('Upload thumbnail') }}</label>
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

            $('.add-product-faq-btn').on('click',function(){
                $('#parent-faq-div').append(
                    `<div class="row faq-individual">
                        <div class="col-md-4 form-group">
                            <input type="text" value="" name="faq[question][]" class="form-control" placeholder="{{ \App\CPU\translate('Question') }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="text" value="" name="faq[answer][]" class="form-control" placeholder="{{ \App\CPU\translate('Answer') }}">
                        </div>
                        <div class="col-md-2 form-group faq-add-main-btn">
                            <i class="tio-delete-outlined text-danger remove-product-faq-btn mt-0"></i>
                        </div>
                    </div>`
                );
            });

            $(document).on('click','.remove-product-faq-btn',function(){
                $(this).closest('.faq-individual').remove();
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
