@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Email Templates List'))

@push('css_or_js')

@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-3">
        <h2 class="h1 mb-0 text-capitalize d-flex gap-2">
            <img src="{{asset('/public/assets/back-end/img/inhouse-product-list.png')}}" alt="">
            Email Templates List
        </h2>
    </div>
    <!-- End Page Title -->

    <div class="row mt-20">
        <div class="col-md-12">
            <div class="card">
                <div class="table-responsive">
                    <table id="datatable" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};" class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                        <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{\App\CPU\translate('SL')}}</th>
                                <th>{{\App\CPU\translate('Template Name')}}</th>
                                <th>{{\App\CPU\translate('Subject')}}</th>
                                <th>{{\App\CPU\translate('status')}}</th>
                                <th>{{\App\CPU\translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($email_templates as $k=>$p)
                            <tr>
                                <th scope="row">{{$email_templates->firstItem()+$k}}</th>
                                <td>
                                    <a href="javascript:void(0);" class="media gap-2">
                                        <span class="media-body title-color">
                                            {{\Illuminate\Support\Str::limit($p['name'],20)}}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:void(0);" class="media gap-2">
                                        <span class="media-body title-color">
                                            {{\Illuminate\Support\Str::limit($p['subject'],20)}}
                                        </span>
                                    </a>
                                </td>
                                <td>
                                    @if($p->status == 1)
                                        <span class="badge badge-success">Active</span>
                                    @else 
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-outline--primary btn-sm square-btn"
                                            title="{{\App\CPU\translate('Edit')}}"
                                            href="{{route('admin.business-settings.mail.template-edit',[$p['id']])}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive mt-4">
                    <div class="px-4 d-flex justify-content-lg-end">
                        <!-- Pagination -->
                        {{$email_templates->links()}}
                    </div>
                </div>

                @if(count($email_templates)==0)
                    <div class="text-center p-4">
                        <img class="mb-3 w-160" src="{{asset('public/assets/back-end')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{\App\CPU\translate('No data to show')}}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    
@endpush
