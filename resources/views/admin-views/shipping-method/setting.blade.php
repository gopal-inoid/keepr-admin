@extends('layouts.back-end.app')

@section('content')
<div class="content container-fluid">
    <!-- Page Title -->
    <div class="mb-4 pb-2">
        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center">
            <img class="mr-2" src="{{asset('assets/back-end/img/Shipping_solid.svg')}}" alt="">
            {{\App\CPU\translate('Shipping Method')}}
            <span class="badge badge-soft-dark radius-50 fz-12">{{-- $shipping_methods->count() --}} 2</span>
        </h2>
    </div>
    <!-- End Page Title -->

    <!-- Inlile Menu -->
    {{-- @include('admin-views.business-settings.business-setup-inline-menu') --}}
    <!-- End Inlile Menu -->

    <div class="row gy-3" >
        <div class="col-12" id="order_wise_shipping">
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="px-3 py-4">
                            <div class="row justify-content-between align-items-center flex-grow-1">
                                <div class="col-sm-4 col-md-6 col-lg-8 mb-2 mb-sm-0">
                                    <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">
                                        {{\App\CPU\translate('Shipping Methods')}}
                                       
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive pb-3">
                            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table" cellspacing="0">
                                <thead class="thead-light thead-50 text-capitalize">
                                    <tr>
                                        <th>{{\App\CPU\translate('sl')}}</th>
                                        <th>{{\App\CPU\translate('title')}}</th>
                                        <th>Regular duration</th>
                                        <th>{{\App\CPU\translate('express duration')}}</th>
                                        <th class="text-center">{{\App\CPU\translate('status')}}</th>
                                        <th class="text-center">{{\App\CPU\translate('action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($shipping_methods as $k=>$method)
                                    <tr>
                                        <th>{{$k+1}}</th>
                                        <td>{{$method['title']}}</td>
                                        <td>
                                            {{$method['normal_duration']}}
                                        </td>
                                        <td>
                                            {{$method['express_duration']}}
                                        </td>
                                        <td>
                                            <label class="switcher mx-auto">
                                                <input type="checkbox" class="switcher_input status"
                                                    id="{{$method['id']}}" {{$method->status == 1?'checked':''}}>
                                                <span class="switcher_control"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap justify-content-center gap-10">
                                                <a  class="btn btn-outline--primary btn-sm edit"
                                                title="{{ \App\CPU\translate('Edit')}}"
                                                href="{{route('admin.business-settings.shipping-method.edit',[$method['id']])}}">
                                                    <i class="tio-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    $( document ).ready(function() {

        // let shipping_responsibility ='{{--$shippingMethod--}}';
        // console.log(shipping_responsibility);
        // if(shipping_responsibility === 'sellerwise_shipping')
        // {
        //     $("#for_inhouse_deliver").show();
        // }else{
        //     $("#for_inhouse_deliver").hide();
        // }
        // let shipping_type = '{{--$shippingType--}}';

        // if(shipping_type==='category_wise')
        // {
        //     $('#product_wise_note').hide();
        //     $('#order_wise_shipping').hide();
        //     $('#update_category_shipping_cost').show();

        // }else if(shipping_type==='order_wise'){
        //     $('#product_wise_note').hide();
        //     $('#update_category_shipping_cost').hide();
        //     $('#order_wise_shipping').show();
        // }else{

        //     $('#update_category_shipping_cost').hide();
        //     $('#order_wise_shipping').hide();
        //     $('#product_wise_note').show();
        // }

    });
</script>
<script>
    function shipping_responsibility(val){
        if(val=== 'inhouse_shipping'){
            $( "#sellerwise_shipping" ).prop( "checked", false );
            $("#for_inhouse_deliver").hide();
        }else{
            $( "#inhouse_shipping" ).prop( "checked", false );
            $("#for_inhouse_deliver").show();
        }
        console.log(val);
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.shipping-method.shipping-store')}}",
                method: 'POST',
                data: {
                    shippingMethod: val
                },
                success: function (data) {


                        //window.location.reload();
                        toastr.success("{{\App\CPU\translate('shipping_responsibility_updated_successfully!!')}}");

                }
            });
    }
</script>
<script>
    function shipping_type(val)
    {
        console.log(val);
        if(val==='category_wise')
        {
            $('#product_wise_note').hide();
            $('#order_wise_shipping').hide();
            $('#update_category_shipping_cost').show();
        }else if(val==='order_wise'){
            $('#product_wise_note').hide();
            $('#update_category_shipping_cost').hide();
            $('#order_wise_shipping').show();
        }else{
            $('#update_category_shipping_cost').hide();
            $('#order_wise_shipping').hide();
            $('#product_wise_note').show();
        }

        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{route('admin.business-settings.shipping-type.store')}}",
                method: 'POST',
                data: {
                    shippingType: val
                },
                success: function (data) {
                    toastr.success("{{\App\CPU\translate('shipping_method_updated_successfully!!')}}");
                }
            });
    }
</script>
<script>
    // Call the dataTables jQuery plugin
    $(document).on('change', '.status', function () {
        var id = $(this).attr("id");
        if ($(this).prop("checked") == true) {
            var status = 1;
        } else if ($(this).prop("checked") == false) {
            var status = 0;
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.business-settings.shipping-method.status-update')}}",
            method: 'POST',
            data: {
                id: id,
                status: status
            },
            success: function () {
                toastr.success('{{\App\CPU\translate('order wise shipping method Status updated successfully')}}');
            }
        });
    });
    // $(document).on('click', '.delete', function () {
    //     var id = $(this).attr("id");
    //     Swal.fire({
    //         title: '{{\App\CPU\translate('Are you sure delete this')}} ?',
    //         text: "{{\App\CPU\translate('You will not be able to revert this')}}!",
    //         showCancelButton: true,
    //         confirmButtonColor: '#3085d6',
    //         cancelButtonColor: '#d33',
    //         confirmButtonText: '{{\App\CPU\translate('Yes, delete it')}}!',
    //         type: 'warning',
    //         reverseButtons: true
    //     }).then((result) => {
    //         if (result.value) {
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
    //                 }
    //             });
    //             $.ajax({
    //                 url: "{{route('admin.business-settings.shipping-method.delete')}}",
    //                 method: 'POST',
    //                 data: {id: id},
    //                 success: function () {
    //                     toastr.success('{{\App\CPU\translate('Order Wise Shipping Method deleted successfully')}}');
    //                     location.reload();
    //                 }
    //             });
    //         }
    //     })
    // });
</script>
@endpush
