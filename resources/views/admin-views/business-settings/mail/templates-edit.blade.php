@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Edit Email Template'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .variable-style {
            font-size: 16px;
            padding: 5px;
            font-weight: bold;
        }

        #variable-main-div {
            max-height: 495px;
            overflow-x: hidden;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid ">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{ asset('/assets/back-end/img/Email_Solid.svg') }}" alt="">
                {{ \App\CPU\translate('Edit Email Template') }}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.business-settings.mail.templates-update') }}"
                    method="POST" style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data" id="product_form">
                    @csrf
                    <input type="hidden" value="{{ $email_templates->id }}" name="id">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="lang_form" id="english-form">
                                        <div class="form-group">
                                            <label class="title-color"
                                                for="name">{{ \App\CPU\translate('Template Name (For internal Use only)') }}
                                            </label>
                                            <input type="text" required name="name" id="name" disabled="disabled"
                                                value="{{ $email_templates->name }}" class="form-control"
                                                placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="description">{{ \App\CPU\translate('Description (For internal Use only)') }}
                                        </label>
                                        <input type="text" required name="description" id="description"
                                            value="{{ $email_templates->description }}" class="form-control"
                                            placeholder="Description">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="title-color" for="subject">{{ \App\CPU\translate('Email Subject') }}
                                        </label>
                                        <input type="text" required name="subject"
                                            value="{{ $email_templates->subject }}" id="subject" class="form-control"
                                            placeholder="Subject">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="title-color" for="subject">{{ \App\CPU\translate('Status') }}
                                        </label>
                                        <label class="switcher">
                                            <input type="checkbox" class="status switcher_input" name="status"
                                                value="1" {{ $email_templates->status == 1 ? 'checked' : '' }}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label class="title-color"
                                            for="body">{{ \App\CPU\translate('Email Body') }}</label>
                                        <textarea name="body" id="body" class="textarea editor-textarea">{{ $email_templates->body }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="title-color">Email Variables <br> (<small class="title-color"
                                            for="body">Click on any variable to insert between email Subject or
                                            Body.</small>)</label>
                                    <div class="form-group" id="variable-main-div">
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{STATUS}">
                                                {STATUS}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TOTAL_AMOUNT}">
                                                {TOTAL_AMOUNT}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TOTAL_QTY}">
                                                {TOTAL_QTY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TAX_AMOUNT}">
                                                {TAX_AMOUNT}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{PRICE}">
                                                {PRICE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{GRAND_TOTAL}">
                                                {GRAND_TOTAL}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_INFO}">
                                                {SHIPPING_INFO}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_AMOUNT}">
                                                {SHIPPING_AMOUNT}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TAX_INFO}">
                                                {TAX_INFO}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{USERNAME}">
                                                {USERNAME}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{ORDER_ID}">
                                                {ORDER_ID}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{PRODUCT_NAME}">
                                                {PRODUCT_NAME}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{DEVICE_UUID}">
                                                {DEVICE_UUID}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{QTY}">
                                                {QTY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TOTAL_PRICE}">
                                                {TOTAL_PRICE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{COMPANY_NAME}">
                                                {COMPANY_NAME}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{COMPANY_LOGO}">
                                                {COMPANY_LOGO}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{ORDER_DATE}">
                                                {ORDER_DATE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{ORDER_NOTE}">
                                                {ORDER_NOTE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_NAME}">
                                                {BILLING_NAME}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_EMAIL}">
                                                {BILLING_EMAIL}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_ADDRESS}">
                                                {BILLING_ADDRESS}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_CITY}">
                                                {BILLING_CITY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_STATE}">
                                                {BILLING_STATE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_COUNTRY}">
                                                {BILLING_COUNTRY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{BILLING_ZIP}">
                                                {BILLING_ZIP}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_NAME}">
                                                {SHIPPING_NAME}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_EMAIL}">
                                                {SHIPPING_EMAIL}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_ADDRESS}">
                                                {SHIPPING_ADDRESS}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_CITY}">
                                                {SHIPPING_CITY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_STATE}">
                                                {SHIPPING_STATE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_COUNTRY}">
                                                {SHIPPING_COUNTRY}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPPING_ZIP}">
                                                {SHIPPING_ZIP}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{SHIPMENT_INFORMATION}">
                                                {SHIPMENT_INFORMATION}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{ESTIMATED_DELIVERY_DATE}">
                                                {ESTIMATED_DELIVERY_DATE}
                                            </a>
                                        </div>
                                        <div class="list-group mt-1">
                                            <a href="#"
                                                class="variable var-common mx-1 list-group-item list-group-item-action"
                                                data-value="{TRACKING_ID}">
                                                {TRACKING_ID}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-end m-2">
                                <button type="submit"
                                    class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    {{-- ck editor --}}
    {{-- <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/ckeditor.js"></script>
    <script src="{{ asset('/') }}vendor/ckeditor/ckeditor/adapters/jquery.js"></script> --}}

    <link type="text/css" rel="stylesheet" href="{{ asset('jodit/jodit.min.css') }}" />
    <script src="{{ asset('jodit/jodit.min.js') }}" type="text/javascript"></script>

    <script>
        // $('.textarea').ckeditor({
        //     contentsLangDirection: '{{ Session::get('direction') }}',
        // });

        var editor = new Jodit("#body", {
            "spellcheck": false,
            "defaultMode": "1",
            "toolbarAdaptive": false,
            "showXPathInStatusbar": false,
            "height": 500,
            "buttons": "source,|,bold,strikethrough,underline,italic,|,superscript,subscript,|,ul,ol,|,outdent,indent,|,font,fontsize,brush,paragraph,|,image,link,hr,|,undo,redo,selectall,fullsize"
        });

        var common = document.querySelectorAll('.var-common');

        common.forEach(box => {
            box.addEventListener('click', function handleClick(e) {
                e.preventDefault();
                //console.log('box clicked', event);
                var value = box.getAttribute('data-value');
                //console.log(document);
                // if (document.getElementById('subject') === document.activeElement) {
                //     console.log(value);
                // }
                editor.selection.insertHTML(value);
            });
        });

        // Alphabetical ascending order to all Email variables
        $(document).ready(function() {
            var container = $("#variable-main-div");
            var listItems = container.find(".list-group-item");
            listItems.sort(function(a, b) {
                var aValue = $(a).data("value");
                var bValue = $(b).data("value");
                return aValue.localeCompare(bValue);
            });
            container.html(listItems);
        });
    </script>

    {{-- ck editor --}}
@endpush
