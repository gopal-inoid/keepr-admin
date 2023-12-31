@extends('layouts.back-end.app')

@section('title', \App\CPU\translate('Profile Settings'))

@push('css_or_js')
<link href="{{asset('public/assets/back-end/css/croppie.css')}}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/css/intlTelInput.css" />
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<!-- Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-end">
            <h2 class="col-sm mb-2 mb-sm-0 h1 mb-0 text-capitalize d-flex align-items-center gap-2">
                <img width="20" src="{{asset('/public/assets/back-end/img/profile_setting.png')}}" alt="">
                {{\App\CPU\translate('Settings')}}
            </h2>

            <div class="col-sm-auto">
                <a class="btn btn--primary" href="{{route('admin.dashboard')}}">
                    <i class="tio-home mr-1"></i> {{\App\CPU\translate('Dashboard')}}
                </a>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Page Header -->

    <div class="row">
        <div class="col-lg-3">
            <!-- Navbar -->
            <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                <!-- Navbar Toggle -->
                <button type="button" class="navbar-toggler btn btn-block btn-white mb-3" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu" data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                    <span class="d-flex justify-content-between align-items-center">
                        <span class="h5 mb-0">{{\App\CPU\translate('Nav menu')}}</span>

                        <span class="navbar-toggle-default">
                            <i class="tio-menu-hamburger"></i>
                        </span>

                        <span class="navbar-toggle-toggled">
                            <i class="tio-clear"></i>
                        </span>
                    </span>
                </button>
                <!-- End Navbar Toggle -->

                <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                    <!-- Navbar Nav -->
                    <ul id="navbarSettings" class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="javascript:" id="generalSection">
                                <i class="tio-user-outlined nav-icon"></i>{{\App\CPU\translate('Basic')}} {{\App\CPU\translate('information')}}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="javascript:" id="passwordSection">
                                <i class="tio-lock-outlined nav-icon"></i> {{\App\CPU\translate('Password')}}
                            </a>
                        </li>
                    </ul>
                    <!-- End Navbar Nav -->
                </div>
            </div>
            <!-- End Navbar -->
        </div>

        <div class="col-lg-9">
            <form action="{{route('admin.profile.update',[$data->id])}}" method="post" enctype="multipart/form-data" id="admin-profile-form">
                @csrf
                <!-- Card -->
                <div class="card mb-3 mb-lg-5" id="generalDiv">
                    <!-- Profile Cover -->
                    <div class="profile-cover">
                        @php($shop_banners = $shop_banner ? asset('storage/app/public/shop/'.$shop_banner) : 'https://images.pexels.com/photos/866398/pexels-photo-866398.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1')
                        <div class="profile-cover-img-wrapper" style="background-image: url({{ $shop_banners }}); background-repeat: no-repeat; background-size: cover;"></div>
                    </div>
                    <!-- End Profile Cover -->

                    <!-- Avatar -->
                    <label class="avatar avatar-xxl avatar-circle avatar-border-lg profile-cover-avatar" for="avatarUploader">
                        <img id="viewer" onerror="this.src='{{asset('public/assets/back-end/img/160x160/img1.jpg')}}'" class="avatar-img" src="{{url('/public/admin')}}/{{$data->image}}" alt="Image">
                    </label>
                    <!-- End Avatar -->
                </div>
                <!-- End Card -->

                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <div class="card-header">
                        <h2 class="card-title h4">{{\App\CPU\translate('Basic')}} {{\App\CPU\translate('information')}}</h2>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Form -->
                        <!-- Form Group -->
                        <div class="row form-group">
                            <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">
                                {{\App\CPU\translate('Full')}} {{\App\CPU\translate('name')}}
                                <i class="tio-help-outlined text-body ml-1" title="Display name">
                                </i>
                            </label>

                            <div class="col-sm-9">
                                <div class="input-group input-group-sm-down-break">
                                    <input type="text" class="form-control" name="name" id="firstNameLabel" placeholder="{{\App\CPU\translate('Your first name')}}" aria-label="Your first name" value="{{$data->name}}">

                                </div>
                            </div>
                        </div>
                        <!-- End Form Group -->

                        <!-- Form Group -->
                        <div class="row form-group">
                            <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('Phone')}} <span class="input-label-secondary">({{\App\CPU\translate('Optional')}})</span></label>
                            @if(!empty($data->phone_code))
                                <?php
                                        $phonecode = explode('+',$data->phone_code);
                                        if(!empty($phonecode[count($phonecode)-1])){
                                            $codeadded = '+'.$phonecode[count($phonecode)-1];
                                        }
                                ?>
                            @endif
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="title-color d-flex">Phone Code</label>
                                    <input class="form-control txtPhone" name="phone_code" type="tel" id="txtPhone" class="txtbox" value="{{($codeadded ?? '')}}" />
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="title-color">Phone</label>
                                <div class="form-group">
                                    <input type="number" class="form-control" name="phone" id="phoneLabel" placeholder="+x(xxx)xxx-xx-xx" value="{{$data->phone}}">
                                </div>
                            </div>
                        </div>
                        <!-- End Form Group -->

                        <div class="row form-group">
                            <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{\App\CPU\translate('Email')}}</label>

                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email" id="newEmailLabel" value="{{$data->email}}" placeholder="{{\App\CPU\translate('Enter new email address')}}" aria-label="Enter new email address">
                            </div>
                        </div>
                        <div class="row">
                            <label for="newEmailLabel" class="col-sm-3 input-label">{{\App\CPU\translate('Profile_Image')}}</label>
                            <div class="form-group col-md-9" id="select-img">
                                <span class="d-block mb-2 text-info">( {{\App\CPU\translate('ratio')}} 1:1 )</span>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileUpload" class="custom-file-input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileUpload">{{\App\CPU\translate('image')}} {{\App\CPU\translate('Upload')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" onclick="{{env('APP_MODE')!='demo'?"form_alert('admin-profile-form','Want to update admin info ?')":"call_demo()"}}" class="btn btn--primary">{{\App\CPU\translate('Save changes')}}</button>
                        </div>
                        <!-- End Form -->
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </form>

            <!-- Card -->
            <div id="passwordDiv" class="card mb-3 mb-lg-5">
                <div class="card-header">
                    <h4 class="card-title">{{\App\CPU\translate('Change')}} {{\App\CPU\translate('your')}} {{\App\CPU\translate('password')}}</h4>
                </div>

                <!-- Body -->
                <div class="card-body">
                    <!-- Form -->
                    <form id="changePasswordForm" action="{{route('admin.profile.settings-password')}}" method="post" enctype="multipart/form-data">
                        @csrf

                        <!-- Form Group -->
                        <div class="row form-group">
                            <label for="newPassword" class="col-sm-3 col-form-label input-label"> {{\App\CPU\translate('New')}}
                                {{\App\CPU\translate('password')}}</label>

                            <div class="col-sm-9">
                                <input type="password" class="js-pwstrength form-control" name="password" id="newPassword" placeholder="{{\App\CPU\translate('Enter new password')}}" aria-label="Enter new password" data-hs-pwstrength-options='{
                                           "ui": {
                                             "container": "#changePasswordForm",
                                             "viewports": {
                                               "progress": "#passwordStrengthProgress",
                                               "verdict": "#passwordStrengthVerdict"
                                             }
                                           }
                                         }'>

                                <p id="passwordStrengthVerdict" class="form-text mb-2"></p>

                                <div id="passwordStrengthProgress"></div>
                            </div>
                        </div>
                        <!-- End Form Group -->

                        <!-- Form Group -->
                        <div class="row form-group">
                            <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label"> {{\App\CPU\translate('Confirm')}}
                                {{\App\CPU\translate('password')}} </label>

                            <div class="col-sm-9">
                                <div class="mb-3">
                                    <input type="password" class="form-control" name="confirm_password" id="confirmNewPasswordLabel" placeholder="{{\App\CPU\translate('Confirm your new password')}}" aria-label="Confirm your new password">
                                </div>
                            </div>
                        </div>
                        <!-- End Form Group -->

                        <div class="d-flex justify-content-end">
                            <button type="button" onclick="{{env('APP_MODE')!='demo'?"form_alert('changePasswordForm','Want to update admin password ?')":"call_demo()"}}" class="btn btn--primary">{{\App\CPU\translate('Save')}} {{\App\CPU\translate('changes')}}</button>
                        </div>
                    </form>
                    <!-- End Form -->
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->

            <!-- Sticky Block End Point -->
            <div id="stickyBlockEndPoint"></div>
        </div>
    </div>
    <!-- End Row -->
</div>
@endsection
@push('script_2')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#viewer').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    // function validatePhoneNumber(input_str) {
    //     var re = /^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/;
    //     //console.log(re.test(input_str));
    //     if(re.test(input_str)){
    //         toastr.success('Mobile is correct');
    //     }else{
    //         toastr.error('Wrong Mobile number format');
    //     }
    // }

    $("#customFileUpload").change(function() {
        readURL(this);
    });
</script>

<script>
    $("#generalSection").click(function() {
        $("#passwordSection").removeClass("active");
        $("#generalSection").addClass("active");
        $('html, body').animate({
            scrollTop: $("#generalDiv").offset().top
        }, 2000);
    });

    $("#passwordSection").click(function() {
        $("#generalSection").removeClass("active");
        $("#passwordSection").addClass("active");
        $('html, body').animate({
            scrollTop: $("#passwordDiv").offset().top
        }, 2000);
    });

    $(function() {
        $("#country").change(function() {
            let countryCode = $(this).find('option:selected').data('country-code');
            let value = "+" + $(this).val();
            $('.txtPhone').val(value).intlTelInput("setCountry", countryCode);
        });
        var code = $('.txtPhone').val();
        $('.txtPhone').val(code).intlTelInput();
    });
</script>
@endpush

@push('script')

@endpush