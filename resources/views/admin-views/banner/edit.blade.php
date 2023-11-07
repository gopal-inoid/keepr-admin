@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Banner'))

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Title -->
        <div class="mb-3">
            <h2 class="h1 mb-1 text-capitalize d-flex align-items-center ">
                <img class="mr-2" width="20" src="{{asset('/assets/back-end/img/Banner_solid.svg')}}" alt="">
                {{\App\CPU\translate('banner_update_form')}}
            </h2>
        </div>
        <!-- End Page Title -->

        <!-- Content Row -->
        <div class="row" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.banner.update',[$banner['id']])}}" method="post" enctype="multipart/form-data"
                              class="banner_form">
                            @csrf
                            @method('put')
                            <div class="row align-items-center">
                                <div class="col-md-6 mb-5 mb-lg-0">
                                    <div class="form-group">
                                        <input type="hidden" id="id" name="id">
                                        <label for="name" class="title-color text-capitalize">{{ \App\CPU\translate('banner_URL')}}</label><span class="text-danger">*</span>
                                        <input type="text" name="url" class="form-control" value="{{$banner['url']}}" required
                                        pattern="https?://.+\.(com|in|org)$"
                                        title="Please enter a valid URL (start with http:// or https://)">
                                    </div>
                                    <div class="form-group">
                                    <label for="name"
                                            class="title-color text-capitalize">{{ \App\CPU\translate('banner_type') }}</label>
                                            <input type="text" name="banner_type" readonly class="form-control" id="banner_type" value="Main Banner" required>
                                        </div>
{{-- 
                                    <div class="form-group">
                                        <label for="name" class="title-color text-capitalize">{{\App\CPU\translate('banner_type')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="banner_type" required>
                                            <option value="Main Banner" {{$banner['banner_type']=='Main Banner'?'selected':''}}>Main Banner</option>
                                            <option value="Footer Banner" {{$banner['banner_type']=='Footer Banner'?'selected':''}}>Footer Banner</option>
                                            <option value="Popup Banner" {{$banner['banner_type']=='Popup Banner'?'selected':''}}>Popup Banner</option>
                                            <option value="Main Section Banner" {{$banner['banner_type']=='Main Section Banner'?'selected':''}}>{{ \App\CPU\translate('Main Section Banner')}}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="resource_id" class="title-color text-capitalize">{{\App\CPU\translate('resource_type')}}</label>
                                        <select onchange="display_data(this.value)"
                                                class="js-example-responsive form-control w-100"
                                                name="resource_type" required>
                                            <option value="product" {{$banner['resource_type']=='product'?'selected':''}}>Product</option>
                                            <option value="category" {{$banner['resource_type']=='category'?'selected':''}}>Category</option>
                                            <option value="shop" {{$banner['resource_type']=='shop'?'selected':''}}>Shop</option>
                                            <option value="brand" {{$banner['resource_type']=='brand'?'selected':''}}>Brand</option>
                                        </select>
                                    </div> --}}

                                    <div class="form-group" id="resource-product" style="display: {{$banner['resource_type']=='product'?'block':'none'}}">
                                        <label for="product_id" class="title-color text-capitalize">{{\App\CPU\translate('product')}}</label>
                                        <select  class="js-example-responsive form-control w-100"
                                                name="product_id">
                                            @foreach(\App\Model\Product::active()->get() as $product)
                                                <option value="{{$product['id']}}" {{$banner['resource_id']==$product['id']?'selected':''}}>{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="resource-category" style="display: {{$banner['resource_type']=='category'?'block':'none'}}">
                                        <label for="name" class="title-color text-capitalize">{{\App\CPU\translate('category')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="category_id">
                                            @foreach(\App\CPU\CategoryManager::parents() as $category)
                                                <option value="{{$category['id']}}" {{$banner['resource_id']==$category['id']?'selected':''}}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="resource-shop" style="display: {{$banner['resource_type']=='shop'?'block':'none'}}">
                                        <label for="shop_id" class="title-color text-capitalize">{{\App\CPU\translate('shop')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="shop_id">
                                            @foreach(\App\Model\Shop::active()->get() as $shop)
                                                <option value="{{$shop['id']}}" {{$banner['resource_id']==$shop['id']?'selected':''}}>{{$shop['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group" id="resource-brand" style="display: {{$banner['resource_type']=='brand'?'block':'none'}}">
                                        <label for="brand_id" class="title-color text-capitalize">{{\App\CPU\translate('brand')}}</label>
                                        <select class="js-example-responsive form-control w-100"
                                                name="brand_id">
                                            @foreach(\App\Model\Brand::all() as $brand)
                                                <option value="{{$brand['id']}}" {{$banner['resource_id']==$brand['id']?'selected':''}}>{{$brand['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <label for="name">{{ \App\CPU\translate('Image')}}</label><span class="text-danger">*</span><span
                                        class="ml-1 text-info">( {{\App\CPU\translate('ratio')}} 4:1 )</span>
                                    <br>
                                    <div class="custom-file text-left">
                                        <input required type="file" name="image" id="mbimageFileUploader"
                                                class="custom-file-input"
                                                accept=".jpg, .png, .jpeg, .svg, .bmp, .tif, .tiff|image/*">
                                        <label class="custom-file-label"
                                                for="mbimageFileUploader">{{\App\CPU\translate('choose')}} {{\App\CPU\translate('file')}}</label>
                                    </div>
                                    <span class="text-secodary">Allowed file formats: .jpg, .png, .jpeg, .svg, .bmp, .tif, .tiff</span>
                                </div>
                                <div class="col-md-6">
                                    <center class="mb-30 max-w-500 mx-auto">
                                        <img class="ratio-4:1"
                                         id="mbImageviewer"
                                         src="{{asset('/banner')}}/{{$banner['photo']}}"
                                         alt="banner image"
                                         onerror="this.src='{{asset('public/assets/front-end/img/placeholder.png')}}'"
                                         >
                                         <h6 class="text-success mt-2">Image Preview</h6>
                                         
                                    </center>
                                    {{-- <center>
                                        <img  class="upload-img-view"
                                        onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'" style="height:42px !important;"
                                        src="{{asset('/banner')}}/{{$banner['photo']}}"
                                        alt="Logo">
                                    </center> --}}
                                    {{-- <center>
                                        <img
                                            class="upload-img-view"
                                            id="mbImageviewer"
                                            src="{{asset('/banner')}}/{{$banner['photo']}}"
                                            alt=""/>
                                    </center> --}}
                                </div>

                                <div class="col-md-12 mt-3 d-flex justify-content-end">
                                    <button type="reset" class="btn btn-secondary px-4 mr-3">{{ \App\CPU\translate('reset')}}</button>
                                    <button type="submit" class="btn btn--primary px-4">{{ \App\CPU\translate('update')}}</button>
                                </div>
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
        $(".js-example-theme-single").select2({
            theme: "classic"
        });

        $(".js-example-responsive").select2({
            // dir: "rtl",
            width: 'resolve'
        });

        function display_data(data) {

            $('#resource-product').hide()
            $('#resource-brand').hide()
            $('#resource-category').hide()
            $('#resource-shop').hide()

            if (data === 'product') {
                $('#resource-product').show()
            } else if (data === 'brand') {
                $('#resource-brand').show()
            } else if (data === 'category') {
                $('#resource-category').show()
            } else if (data === 'shop') {
                $('#resource-shop').show()
            }
        }
    </script>

    <script>
        function mbimagereadURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#mbImageviewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#mbimageFileUploader").change(function () {
            let imgArray=['image/jpg', 'image/png', 'image/jpeg', 'image/svg', 'image/bmp', 'image/tif', 'image/tiff'];
                let imgType=this.files[0].type;
                if(imgArray.includes(imgType)){
                    $(".custom-file-label").html(this.files[0].name);
                     mbimagereadURL(this);
                }else{
                    this.value="";
                    $(".custom-file-label").html("Invalid file format");
                    $(".custom-file-label").addClass("text-danger  border border-1 border-danger"); 
                    setTimeout(() => {
                     $(".custom-file-label").html("Choose file"); 
                     $(".custom-file-label").removeClass("text-danger  border border-1 border-danger");
                    }, 1000);
                   
                }
        });
    </script>
@endpush
