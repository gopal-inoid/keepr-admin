@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Edit Email Template'))

@push('css_or_js')
    <link href="{{ asset('public/assets/back-end/css/tags-input.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/select2/css/select2.min.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <h2 class="h1 mb-0 d-flex gap-2">
                <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
                {{\App\CPU\translate('Edit Email Template')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row">
            <div class="col-md-12">
                <form class="product-form" action="{{ route('admin.business-settings.mail.templates-update') }}" method="POST"
                    style="text-align: {{ Session::get('direction') === 'rtl' ? 'right' : 'left' }};"
                    enctype="multipart/form-data" id="product_form">
                    @csrf
                    <input type="hidden" value="{{$email_templates->id}}" name="id">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="lang_form" id="english-form">
                                        <div class="form-group">
                                            <label class="title-color" for="name">{{ \App\CPU\translate('Template Name') }}
                                            </label>
                                            <input type="text" required name="name" id="name" value="{{$email_templates->name}}" class="form-control" placeholder="Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="title-color" for="subject">{{ \App\CPU\translate('subject') }}
                                            </label>
                                            <input type="text" required name="subject" value="{{$email_templates->subject}}" id="subject" class="form-control" placeholder="Subject">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="title-color" for="subject">{{ \App\CPU\translate('Status') }}
                                            </label>
                                        <label class="switcher">
                                            <input type="checkbox" class="status switcher_input"
                                                    name="status" value="1" {{$email_templates->status == 1?'checked':''}}>
                                            <span class="switcher_control"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="title-color" for="body">{{ \App\CPU\translate('Template Body') }}</label>
                                        <textarea name="body" id="body" class="textarea editor-textarea">{{$email_templates->body}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end gap-3 mt-3">
                        <button type="submit" class="btn btn--primary">{{ \App\CPU\translate('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')

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
