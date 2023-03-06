@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Payment Method'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-4 pb-2">
            <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img src="{{asset('/public/assets/back-end/img/3rd-party.png')}}" alt="">
                {{\App\CPU\translate('3rd_party')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Inlile Menu -->
    @include('admin-views.business-settings.third-party-inline-menu')
    <!-- End Inlile Menu -->

        <div class="row gy-3">
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-body">
                        @php($config=\App\CPU\Helpers::get_business_settings('stripe'))
                        <form action="{{route('admin.business-settings.payment-method.update',['stripe'])}}"
                              method="post">
                            @csrf
                            @if(isset($config))
                                @php($config['environment'] = $config['environment']??'sandbox')
                                <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
                                    <h5 class="text-uppercase">{{\App\CPU\translate('Stripe')}}</h5>

                                    <label class="switcher show-status-text">
                                        <input class="switcher_input" type="checkbox"
                                               name="status" value="1" {{$config['status']==1?'checked':''}}>
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>

                                <center class="mb-3">
                                    <img src="{{asset('/public/assets/back-end/img/stripe.png')}}" alt="">
                                </center>

                                <div class="form-group">
                                    <label
                                        class="d-flex title-color">{{\App\CPU\translate('choose_environment')}}</label>
                                    <select class="js-example-responsive form-control" name="environment">
                                        <option
                                            value="sandbox" {{$config['environment']=='sandbox'?'selected':''}}>{{\App\CPU\translate('sandbox')}}</option>
                                        <option
                                            value="live" {{$config['environment']=='live'?'selected':''}}>{{\App\CPU\translate('live')}}</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="d-flex title-color">{{\App\CPU\translate('published_key')}}</label>
                                    <input type="text" class="form-control" name="published_key"
                                           value="{{env('APP_MODE')=='demo'?'':$config['published_key']}}">
                                </div>

                                <div class="form-group">
                                    <label class="d-flex title-color">{{\App\CPU\translate('api_key')}}</label>
                                    <input type="text" class="form-control" name="api_key"
                                           value="{{env('APP_MODE')=='demo'?'':$config['api_key']}}">
                                </div>
                                <div class="mt-3 d-flex flex-wrap justify-content-end gap-10">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                            onclick="{{env('APP_MODE')!='demo'?'':'call_demo()'}}"
                                            class="btn btn--primary px-4 text-uppercase">{{\App\CPU\translate('save')}}</button>
                                    @else
                                        <button type="submit"
                                                class="btn btn--primary px-4 text-uppercase">{{\App\CPU\translate('Configure')}}</button>
                                    @endif
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function copyToClipboard(element) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($(element).text()).select();
            document.execCommand("copy");
            $temp.remove();
            toastr.success("{{\App\CPU\translate('Copied to the clipboard')}}");
        }
    </script>
@endpush
