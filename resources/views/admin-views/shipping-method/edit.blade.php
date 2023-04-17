@extends('layouts.back-end.app')

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{\App\CPU\translate('Dashboard')}}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{\App\CPU\translate('Shipping Method Update')}}</li>
        </ol>
    </nav> -->


    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">
            <img src="{{asset('/public/assets/back-end/img/business-setup.png')}}" alt="">
            {{\App\CPU\translate('Shipping_Method_Update')}} ( {{ $method['title'] ?? '' }} )
        </h2>
    </div>
    <!-- End Page Title -->

    <!-- Page Heading -->
    <!-- <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-black-50">{{\App\CPU\translate('shipping_method')}} {{\App\CPU\translate('update')}}</h1>
    </div> -->

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{route('admin.business-settings.shipping-method.update',[$method['id']])}}" method="post">
                    @csrf
                    @method('put')
                    <div class="card-header">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="title">{{\App\CPU\translate('name')}}</label>
                                <input type="text" name="title" class="form-control" value="{{$method['title'] ?? ''}}" placeholder="Enter Shipping Company name">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="title">{{\App\CPU\translate('normal delivery days')}}</label>
                                <input type="text" name="normal_duration" value="{{$method['normal_duration'] ?? ''}}" class="form-control" placeholder="Ex (5-6 days)">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label for="title">{{\App\CPU\translate('express delivery days')}}</label>
                                <input type="text" name="express_duration" value="{{$method['express_duration'] ?? ''}}" class="form-control" placeholder="Ex (2-5 days)">
                            </div>
                        </div>
                        <div class="col-2">
                            <a href="{{route('admin.business-settings.shipping-method.setting')}}" class="btn btn--primary px-4">Back</a>
                            <button type="submit" class="btn btn--primary px-4">{{\App\CPU\translate('Update')}}</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive pb-3">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" cellspacing="0">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>Country</th>
                                        <th>Normal Rate</th>
                                        <th>Express Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($countries_list as $k=>$name)
                                    <tr>
                                        <th>{{$name->country_code}}
                                        <input type="hidden" value="{{$name->country_code}}" name="country[{{$k}}][name]">
                                        </th>
                                        <td><input type="number" min="0" max="1000000" value="{{$name->normal_rate}}" name="country[{{$k}}][normal_rate]" class="form-control"
                                                placeholder="{{\App\CPU\translate('Ex')}} : {{\App\CPU\translate('10')}} "></td>
                                        <td>
                                            <input type="number" min="0" max="1000000" value="{{$name->express_rate}}" name="country[{{$k}}][express_rate]" class="form-control"
                                                placeholder="{{\App\CPU\translate('Ex')}} : {{\App\CPU\translate('10')}} ">
                                        </td>
                                        <td>
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" name="country[{{$k}}][status]" class="switcher_input" value="1" {{$name->status == 1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex gap-10 flex-wrap justify-content-end">
                            <a href="{{route('admin.business-settings.shipping-method.setting')}}" class="btn btn--primary px-4">Back</a>
                            <button type="submit" class="btn btn--primary px-4">{{\App\CPU\translate('Update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')

@endpush
