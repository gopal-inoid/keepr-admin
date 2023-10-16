<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keepr App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('keepr_app_assets/style.css') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('public/company/Keepr-logo-black.png') }}">
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KSPYGQDXX1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-KSPYGQDXX1');
    </script>
</head>

<body data-spy="scroll" data-target="#navbarScrollSpy" data-offset="20">
    <!-- <div> -->
    <div class="Navbar-Section" id="navbar-section">
        <nav class="navbar container navbar-expand-lg navbar-dark p-3 p-lg-0" id="navbarScrollSpy">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url()->current() }}">
                    <img src="{{ asset('keepr_app_assets/assests/Keepe_logo.png') }}" class="img" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto m-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                        <li class="nav-item">
                            <a class="nav-link home" aria-current="page" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link about" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link features" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link products" href="#products">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link faq" href="#faq">FAQ</a>
                        </li>
                    </ul>
                    <form class="d-flex m-3">
                        <a class="btn download_keeper_btn" href="#downloads">Download Keepr</a>
                    </form>
                </div>
            </div>
        </nav>
    </div>
    <!-- </div> -->


    <div class="container-fluid">
        <div class="section_seocond" id="home">
            <div class="For_picture">
                <div class="container" id="Valuables_again">
                    <div class="row">
                        <div class="leftKeeprPic col-xl-5 col-md-5" id="leftKeeprPic">
                            <div class="Main_Content">
                                <span class="main_heading">Always keep your stuff with you</span>
                                <p>Do you always lose or forget your stuff before you go out? Keeper can help with that
                                    by alerting you when you leave your things behind!</p>
                                <div class="download_app_btn">
                                    <a href="#downloads" class="btn">Download Keepr</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-7 col-md-7">
                            <div class="rightMobileIcon">
                                <img src="{{ asset('keepr_app_assets/assests/KeeprDistance.png') }}" alt="">

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="container-fluid section_three" id="about">
            <div class="container">
                <div class="About_seocond row">
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="rightMobileIcon2">
                            <img src="{{ asset('keepr_app_assets/assests/Keepr-about.png') }}" alt="">
                        </div>
                    </div>
                    <div class="leftKeeprPic col-md-6">
                        <div class="ms-1 About_Content">About</div>
                        <div class="About_Main_Content">
                            <span class="Ultimate_Bluetooth_Finder">Keepr: Stop Losing Your Stuff</span>
                            <!-- <p>Keepr was founded and designed with one goal in mind: We wanted to make sure that when
                                you go out, you don’t lose your stuff.</p>
                            <p>Let’s say you went out for lunch and have keys or a purse that you mistakenly forgot. By
                                attaching our tracker to the keys or leaving it inside the purse and pairing it with our
                                application, the moment you attempt to leave without it, our application will alert you
                                so that you can retrieve your valuable(s) before leaving.</p> -->
                            {!! $about_us !!}
                            <div class="download_app_btn_About">
                                <a href="#downloads" class="btn" type="button ">Download Keepr</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container step_section_row mt-5">
                    <div class="step_section row">
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12 p-md-0">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        1
                                    </div>
                                    <div class="Steps_Content col">
                                        Download our application
                                    </div>
                                </div>
                                <p class="">From the Apple app Store or Google Play Store
                                </p>
                            </div>
                            <div class="arrow-icon col-md-3" id="_block_2">

                                <img src="{{ asset('keepr_app_assets/assests/ArrowIcon.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12 p-md-0">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        2
                                    </div>
                                    <div class="Steps_Content col">
                                        Register and purchase one of our bluetooth fobs
                                    </div>
                                </div>
                                <p class=" mt-2">Make your account and pick from the fobs we offer
                                </p>
                            </div>
                            <div class="arrow-icon col-md-3" id="_block_2">
                                <img src="{{ asset('keepr_app_assets/assests/ArrowIcon.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12 p-md-0">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        3
                                    </div>
                                    <div class="Steps_Content col">
                                        Pair the fob with the application
                                    </div>
                                </div>
                                <p class=" mt-1">Once pairing is complete, you are all set! You will be alerted as
                                    soon
                                    as you leave the area that you forgot the fob.</p>
                            </div>
                            <div class="arrow-icon col-md-1" id="_block_2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid Four_Section" id="features">
            <div class="container">
                <div class="Unlock_Next_Level mb-4">Why Keepr?</div>
                <div class="row">
                    <!-- <div class="col-md-12 col-lg-1 col-xl-1"></div> -->
                    <!-- <div class="col-md-1"></div> -->
                    <div class="col-xl-8 col-md-7 pt-5 ps-lg-5 ps-md-5">
                        <div class="desc-1 active-item p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Everything from the fob purchase process to the pairing process is made easy so that you
                                can go out and have peace of mind that your belongings are safe.</p>
                        </div>
                        <div class="desc-2 pt-3 p-3">
                            <h5>Don’t worry about having the Application Open</h5>
                            <p>Don’t worry about keeping the application open all the time. Keepr will continue to work
                                in the background to alert you if you forgot something.</p>
                        </div>
                        <div class="desc-3 pt-3 p-3">
                            <h5>Lose your stuff anyway?</h5>
                            <p>By enabling your location services, we will be able to provide you with the last known
                                location of your fob so you can find your things, should you lose them.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-5 pt-4 mobile-icon d-flex align-items-center">
                        <img id="AboutImage" src="{{ asset('keepr_app_assets/assests/Keepr-Features.png') }}" alt="">
                    </div>
                </div>

            </div>
        </div>


        <div class="container-fluid pt-5">
            <div class="overlay">
                <div class="overlay-image">
                    <img src="{{ asset('keepr_app_assets/assests/PlayButtonIcon.png') }}" id="overlay-img" class="video_Play_Button" alt="video_Play_Button">

                </div>
            </div>
        </div>


        <div class="container-fluid Fifth_Section" id="products">
            <div class="container">
                <h1 class="text-center" style="font-weight: 600;">Our Products</h1>
                <div class="row justify-content-evenly mt-5">
                    @if (!empty($Products))
                    @foreach ($Products as $k => $val)
                    <div class="col-md-8 col-lg-5 col-xl-5 Product-1 mx-1 my2">
                        <img src="{{ asset('/product/thumbnail/' . $val['thumbnail']) }}" alt="">
                        <div>
                            <h4 class="ibeacon-headline">{{ $val['name'] }}</h4>
                            <div><span class="Price_Count">${{ $val['purchase_price'] }}</span>
                                <br><small>Does not
                                    include
                                    shipping/taxes</small>
                            </div>
                            <!-- <p class="ibeacon_peragraph">The Keepr duo is smaller fob and has a 1 year battery life. the
                                    application will alert you when the battery needs replacing. it also comes with adhesive
                                    to
                                    stick it on objects as well</p> -->
                            {!! $val['details'] !!}
                            <a href="" class="btn buy_now_btn">Buy Now</a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div class="container mt-lg-5 pt-lg-5 mt-md-5 pt-md-5 Frequently_Questions" id="faq" style="padding-top: 6rem !important;">
                    <h1 class="text-center" style="font-weight: 600">Frequently Asked Questions</h1>
                    <div class="row justify-content-center mt-5 pb-5">
                        @if (!empty($faqs))
                        @foreach ($faqs as $k => $val)
                        <div class="col-md-6 Frequently_Asked_Questions" id="Frequently_Asked_Questions2">
                            <div class="pt-3">
                                <h5>{{ $val['question'] }}</h5>
                                <p>{{ $val['answer'] }}</p>
                            </div>
                            <!-- <div class="pt-3">
                                <h5>How can I edit my personal information?</h5>
                                <p>Once you register and login, you can find and edit your user details at the bottom of
                                    the application under the profiles tab.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Do you have a free trial?</h5>
                                <p>At this time, we unfortunately do not offer a free trial. You may purchase the fob
                                    and use our application, and if you are unhappy or encounter any issues, our
                                    customer support team will be glad to assist you with fixing your problem or getting
                                    a refund.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Why do I have to pay import fees?</h5>
                                <p>Some countries may require you to pay a required shipping fee when
                                    purchasing/ordering a product from another country.</p>
                            </div> -->
                        </div>
                        @endforeach
                        @endif
                        <!-- <div class="col-md-6 Frequently_Asked_Questions mt-lg-0 mt-md-3"
                            id="Frequently_Asked_Questions1">
                            <div>
                                <h5>How long does shipping take?</h5>
                                <p>Shipping varies from country to country, but generally, normal shipping may take
                                    anywhere between 4-12 weeks. We also offer express shipping option at a slightly
                                    higher cost, which can take anywhere between 6-12 business days to deliver.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Can I track my package?</h5>
                                <p>Absolutely! We will provide you with a tracking number via email so that you can
                                    track your shipment with the carrier service.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Is the Keepr fob tracking my position at all times?</h5>
                                <p>No We do not track or monitor your position. Only when you lose your valuables and
                                    click the track option do we check the location of your fob. </p>
                            </div>
                            <div class="pt-3">
                                <h5>Can I pay for the order in cash?</h5>
                                <p>We currently only accept the payment methods accepted by Stripe (Visa, Mastercard,
                                    American Express).</p>
                            </div>
                        </div> -->
                    </div>
                    <h4 class="text-center mt-5 mb-5">
                        <b>Haven't find suitable answer? </b><b class="tellUs"><a href="mailto:support@thekeeprapp.com">Tell us what you need</a></b>
                    </h4>
                </div>
            </div>

            <div class="container-fluid Seven_Section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xl-6 col-md-6 rightSection">
                            <div style="width: 100%;">
                                <h3 class="More_Questions">Have more questions? Don’t hesitate to reach out to us.</h3>
                                <div class="Seven_Section_Social">
                                    <div class="mail_section">
                                        <img src="{{ asset('keepr_app_assets/assests/E-mail_icon.svg') }}" alt="">
                                        <a href="mailto:support@thekeeprapp.com">
                                            &nbsp; support@thekeeprapp.com</a>
                                    </div>
                                    <div class="mail_section" id="mail_section">
                                        <img src="{{ asset('keepr_app_assets/assests/Mobile_Number_Icon.svg') }}" alt="">
                                        <a href="tel:2125551212"> &nbsp; +1
                                            647-614-1496</a>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- <div class="col-md-1"></div> -->
                        <div class="col-xl-4 col-md-5 HeadPhone_image pt-4">
                            <img src="{{ asset('keepr_app_assets/assests/Overlapping_img.png') }}" alt="">

                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid footer">
                <div class="footer_content">
                    <div class="container">
                        <div class="row" style="position: relative;">
                            <div class="col-md-12 footer-body">

                                <img src="{{ asset('keepr_app_assets/assests/Footer_Log.png') }}" class="img" alt="" style="height: 70px;">

                            </div>
                            <div class="footer-body2">
                                <div class="text-center">
                                    <ul class="footer_menu justify-content-center">
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" data="home" aria-current="page" href="#home">Home</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" data="about" href="#about">About</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" data="features" href="#features">Features</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" data="products" href="#products">Products</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" data="faq" href="#faq">FAQ</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{ route('terms-condition') }}">Terms of
                                                Service</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{ route('privacy-policy') }}">Privacy and
                                                Policy</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="container overlaping_div pt-5" id="downloads">
                                <div class="row" id="Overlap-content">
                                    <div class="col-lg-5 col-md-5 col-sm-12 overlaping_div_content">
                                        <h1 class="abs_div_content">
                                            Download the Keepr Mobile App For free
                                        </h1>
                                        <p class="mt-4">Leave us a rating and review!</p>
                                        <div class="app_download_btn">
                                            <a href="https://play.google.com/store/apps/details?id=com.keepr.android" target="_blank" class="m-md-1">
                                                <img src="{{ asset('keepr_app_assets/assests/PlayStore_Icon.svg') }}" alt="">
                                            </a>
                                            <a href="https://apps.apple.com/us/app/keepr-app/id6449671498" target="_blank" class="m-md-1">
                                                <img src="{{ asset('keepr_app_assets/assests/AppStore_Icon.svg') }}" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-7 col-md-7 col-sm-12 text-end">
                                        <img src="{{ asset('keepr_app_assets/assests/TriMobile.png') }}" alt="">

                                    </div>
                                </div>
                            </div>

                            <div class="Copyright_Section pt-4 pb-4">
                                <div class="CopyRight_Section">
                                    <p class="text-light">&#9400; Copyright 2023 Keepr, All Rights Reserved.</p>
                                    <p class="text-muted d-flex justify-content-start align-items-end" style="font-size:15px; text-align:middle;">&nbsp; Design & Developed by&nbsp;<a href="https://inoidsolutions.com/" class="text-light" target="_blank">iNoid
                                            Solutions</a></p>
                                </div>
                                <div class="CopyRight_Section">

                                    <a href="https://www.facebook.com/profile.php?id=61551488266788" target="_blank"><img class="mx-1" src="{{ asset('keepr_app_assets/assests/FaceBook_Icon.svg') }}" alt=""></a>
                                    {{-- <img class="mx-1" src="{{ asset('keepr_app_assets/assests/TwitterIcon.svg') }}"
                                    alt=""> --}}
                                    <a href="https://www.instagram.com/thekeeprapp/" target="_blank"><img class="mx-1" src="{{ asset('keepr_app_assets/assests/InstgramIcon.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header py-0">
                        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                        <button type="button" class="close border-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="keeprVideo" width="100%" height="500" autoplay="true" src="https://www.youtube.com/embed/92OG1DxMJyw?si=saWna070vqcwAPgH?autoplay=1&amp;controls=0&amp;rel=0                                                                                      " title="YouTube video player" frameborder="0" allow="autoplay; encrypted-media;" allowfullscreen></iframe>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
</script>
<script>
    $(document).ready(function() {

        // $(document).on('click', '.nav-link', function(e) {
        //     $('.nav-link').each(function(e) {
        //         $(this).removeClass("active");
        //     });
        //     $(this).addClass("active");
        // });

        // $(document).on('click', '.footer_menu .nav-item .nav-link', function(e) {
        //     let menu = $(this).attr("data");
        //     $('.nav-link').each(function(e) {
        //         $(this).removeClass("active");
        //     });
        //     $("." + menu).addClass('active');
        // });

        $(".desc-1").hover(function() {
            $(this).addClass("active-item");
            $(".desc-2").removeClass("active-item");
            $(".desc-3").removeClass("active-item");
            $(".desc-4").removeClass("active-item");
        })
        $(".desc-2").hover(function() {
            $(this).addClass("active-item");
            $(".desc-1").removeClass("active-item");
            $(".desc-3").removeClass("active-item");
            $(".desc-4").removeClass("active-item");
        })
        $(".desc-3").hover(function() {
            $(this).addClass("active-item");
            $(".desc-2").removeClass("active-item");
            $(".desc-1").removeClass("active-item");
            $(".desc-4").removeClass("active-item");
        });

        var url = $("#keeprVideo").attr('src');
        $("#exampleModalCenter").on('hide.bs.modal', function() {
            $("#keeprVideo").attr('src', '');
        });
        $("#exampleModalCenter").on('show.bs.modal', function() {
            $("#keeprVideo").attr('src', url);
        });

        $('#exampleModalCenter').on('shown.bs.modal', function() {
            $('#keeprVideo')[0].src += "&autoplay=1";
        });

        $('#exampleModalCenter').on('hidden.bs.modal', function() {
            $('#keeprVideo')[0].src = $('#keeprVideo')[0].src.replace("&autoplay=1", "");
        });

        $("#overlay-img").click(function() {
            $('#exampleModalCenter').modal("show");
        })


        // $(window).on("load", function() {
        //     let src=$("#keeprIntro").attr("src");
        //     alert(src);
        // });

    })
</script>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
</script>
<!-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script> -->



</html>