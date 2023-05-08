<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keepr App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('keepr_app_assets/style.css') }}">
    <link rel="icon" type="image/x-icon" href="{{asset("public/company/Keepr-logo-black.png")}}">
</head>

<body>
    <div>
        <div class="Navbar-Section">
            <nav class="navbar container navbar-expand-lg navbar-light pt-0">
                <div class="container-fluid">
                    <a class="navbar-brand" href="#">
                        <img src="{{ asset('keepr_app_assets/assests/Keepe_logo.png')}}" alt="">
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto m-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="#Home">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#About">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#Features">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#Products">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#Frequently_Questions">FAQ</a>
                            </li>
                        </ul>
                        <form class="d-flex mt-3">
                            <a class="btn download_keeper_btn" href="#Download_keepr" type="submit">Download Keepr</a>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </div>


    <div class="container-fluid" style="padding-top: 74px;">
        <div class="section_seocond" id="Home">
            <div class="For_picture">
                <div class="container" id="Valuables_again">
                    <div class="row">
                        <div class="leftKeeprPic col-xl-5 col-md-6" id="leftKeeprPic">
                            <div class="Main_Content">
                                <span class="main_heading">Never Lose Your Valuables again!</span>
                                <p>Do you find yourself in a position where you go out a lot and sometimes lose or
                                    forget your valuables? keepr will help stop that by alerting you when you leave your
                                    things behind </p>
                                <div class="download_app_btn">
                                    <a href="#Download_keepr" class="btn" type="button ">Download keepr</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-xl-7 col-md-6">
                            <div class="rightMobileIcon">
                                <img src="{{ asset('keepr_app_assets/assests/KeeprDistance.png') }}" alt="">

                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
        <div class="container-fluid section_three" id="About">
            <div class="container">
                <div class="About_seocond row">
                    <div class="col-md-6">
                        <div class="rightMobileIcon2">
                            <img src="{{ asset('keepr_app_assets/assests/Keepr-about.png') }}" alt="">
                        </div>
                    </div>
                    <div class="leftKeeprPic col-md-6">
                        <div class="ms-1 About_Content">About</div>
                        <div class="About_Main_Content">
                            <span class="Ultimate_Bluetooth_Finder">Keepr's Solutions to Keeping your things
                                safe</span>
                            <p>keepr was founded and designed with one goal in mind: Me wanted to make sure that when
                                you go out that your valuables come back with you
                            </p>
                            <p>Let’s say you went out for lunch and have keys or a purse that you mistakenly forgot. by
                                attahing our mini fob to the keys or leaving it inside the purse and pairing it with our
                                application. the minute you attempt to have without it our application will alert you so
                                that you can retrieve the item before leaving
                            </p>
                            <div class="download_app_btn_About">
                                <a href="#Download_keepr" class="btn" type="button ">Download keepr</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container step_section_row mt-5">
                    <div class="step_section row">
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        1
                                    </div>
                                    <div class="Steps_Content col">
                                        Download our Application
                                    </div>
                                </div>
                                <p class="">From the apple app Store or Google Play Store
                                </p>
                            </div>
                            <div class="arrow-icon col-md-3" id="_block_2">

                                <img src="{{ asset('keepr_app_assets/assests/ArrowIcon.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        2
                                    </div>
                                    <div class="Steps_Content col">
                                        Register and Purchase one of our Bluetooth Fobs
                                    </div>
                                </div>
                                <p class=" mt-2">Make Your account and pick from the fobs we offer
                                </p>
                            </div>
                            <div class="arrow-icon col-md-3" id="_block_2">
                                <img src="{{ asset('keepr_app_assets/assests/ArrowIcon.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12 steps row ms-2">
                            <div id="_block_1" class="col-lg-12 col-xl-9 col-sm-12 col-md-12">
                                <div class="d-flex row">
                                    <div class="step-circle col-md-2 col-sm-2">
                                        3
                                    </div>
                                    <div class="Steps_Content col">
                                        Pair the fob with the Application
                                    </div>
                                </div>
                                <p class=" mt-1">Once pairing is complete. you are all set and will be
                                    alerted as
                                    you leave that you forgot the fob!
                                </p>
                            </div>
                            <div class="arrow-icon col-md-1" id="_block_2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid Four_Section" id="Features">
            <div class="container">
                <div class="Unlock_Next_Level mb-4"> Unlock Next-Level Amazing Features!</div>
                <div class="row">
                    <!-- <div class="col-md-12 col-lg-1 col-xl-1"></div> -->
                    <!-- <div class="col-md-1"></div> -->
                    <div class="col-xl-8 col-md-7 pt-5 ps-lg-5 ps-md-5">
                        <div class="desc-1 active p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Everything from the fob purchase process to the pairing process is made easy so that you
                                can go out and have peace of mind that your belongings are safe</p>
                        </div>
                        <div class="desc-2 pt-3 p-3">
                            <h5>Don’t worry about having the Application Open</h5>
                            <p>Our Application works in the background to alert if you forget your things. don’t worry
                                about having it open at all time </p>
                        </div>
                        <div class="desc-3 pt-3 p-3">
                            <h5>Lose you stuff anyway?</h5>
                            <p>By enabling location services we will be able to give you a location for the last known
                                place of your job so you can find your things should you loose them.</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-5 pt-4 mobile-icon">
                        <img id="AboutImage" src="{{ asset('keepr_app_assets/assests/Keepr-Features.png') }}" alt="">
                    </div>
                </div>

            </div>
        </div>


        <div class="container-fluid pt-5">
            <div class="overlay">
                <div class="overlay-image">
                    <img src="{{ asset('keepr_app_assets/assests/PlayButtonIcon.png') }}" data-toggle="modal" data-target="#exampleModalCenter" class="video_Play_Button" alt="video_Play_Button">

                </div>
            </div>
        </div>


        <div class="container-fluid Fifth_Section" id="Products">
            <div class="container">
                <h1 class="text-center" style="font-weight: 600;">Our Products</h1>
                <div class="row justify-content-center mt-5">
                    <div class="col-md-12 col-lg-5 col-xl-5 Product-1 mx-1 my2">

                        <img src="{{ asset('keepr_app_assets/assests/KeeprDuoPhoto.png') }}" alt="">

                        <div>
                            <h4 class="ibeacon-headline">Keepr Duo </h4>
                            <div><span class="Price_Count">$19.99</span> <br><small>Does not include
                                    shopping/taxes</small>
                            </div>
                            <p class="ibeacon_peragraph">The Keepr duo is smaller fob and has a 1 year battery life. the
                                application will alert you when the battery needs replacing. it also comes with adhesive
                                to
                                stick it on objects as well</p>
                            <a href="" class="btn buy_now_btn" type="button">Buy Now</a>
                        </div>
                    </div>
                    <div class="col-md-12 col-lg-5 col-xl-5 mt-md-4 mt-sm-4 mt-lg-0 Product-2 mx-1 my-2">

                        <img src="{{ asset('keepr_app_assets/assests/KeeprTriPhoto.jpeg') }}" alt="">
                        <div>
                            <h4 class="ibeacon-headline_2">Keepr Tri</h4>
                            <div><span class="Price_Count">$24.99</span> <br> <small>Does not include
                                    shopping/taxes</small>
                                <p class="ibeacon_peragraph">The Keepr Tri is the larger fob and has a battery life up
                                    to 3
                                    years. the application will atert you when the battery needs replacing. it comes
                                    with a
                                    button to turn on and off the tracking capability</p>
                                <a href="" class="btn buy_now_btn" type="button">Buy Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container mt-lg-5 pt-lg-5 mt-md-5 pt-md-5 Frequently_Questions" id="Frequently_Questions">
                    <h1 class="text-center" style="font-weight: 600">Frequently Asked Questions</h1>
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-6 Frequently_Asked_Questions" id="Frequently_Asked_Questions2">
                            <div class="">
                                <h5>How to install Keepr?</h5>
                                <p>You can find and download the Application form the Apple Store or the Google Play
                                    Store. </p>
                            </div>
                            <div class="pt-3">
                                <h5>How can I edit my personal information?</h5>
                                <p>Once You register and login. you can find and edit you user details at the bottom on
                                    the application under the users tab.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Do you have a free trial?</h5>
                                <p>We do not currently offer a free trial unfortunately. You may purchase the fob and
                                    use our application and if you are unhappy or encounter any issue our customer
                                    support team will glad to assist you with fixing your problem or getting a refund.
                                </p>
                            </div>
                            <div class="pt-3">
                                <h5>Why do have to pay import fees?</h5>
                                <p>Some countries require that when a product is shopping from another country a fee is
                                    required to be paid.</p>
                            </div>
                        </div>
                        <div class="col-md-6 Frequently_Asked_Questions mt-lg-0 mt-md-3" id="Frequently_Asked_Questions1">
                            <div>
                                <h5>How long does shipping take?</h5>
                                <p>Shiiping depends on the country you live in. but generally normal shipping can take
                                    anywhere between 4-12 weeks. we have an express shipping option that will cost
                                    slightly more than can get your order to you within 6-12 bussiness days.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Can i track my package?</h5>
                                <p>Absolutely! We will provide you with a tracking number via email so that you can
                                    track your shipment with the carrier service.</p>
                            </div>
                            <div class="pt-3">
                                <h5>Is the keepr fob tracking my position at all times?</h5>
                                <p>No We do not track or monitor your position. Only when you lose your valuables and
                                    click the track option do we check the location of your fob. </p>
                            </div>
                            <div class="pt-3">
                                <h5>Can i pay for the order in cash?</h5>
                                <p> We currently only accept the payment methods accepted by Stripe (Visa, Mastercard,
                                    American Express)</p>
                            </div>
                        </div>
                    </div>
                    <h6 class="text-center mt-4 mb-5">
                        <b>Haven't find suitable answer? </b><b class="tellUs"><a href="mailto:support@thekeeprapp.com">support@thekeeprapp.com</a></b>
                    </h6>
                </div>
            </div>

            <div class="container-fluid Seven_Section">
                <div class="container">
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-xl-6 col-md-6 rightSection">
                            <div>
                                <h3 class="More_Questions">Have more questions? Don’t hesitate to get in touch!</h3>
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
                            <img src="{{ asset('keepr_app_assets/assests/Overlapping_img.svg') }}" alt="">

                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid footer">
                <div class="footer_content">
                    <div class="container">
                        <div class="row" style="position: relative;">
                            <div class="col-md-12 footer-body">

                                <img src="{{ asset('keepr_app_assets/assests/Footer_Log.svg') }}" alt="">

                            </div>
                            <div class="footer-body2">
                                <div class="text-center">
                                    <ul class="footer_menu justify-content-center">
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" aria-current="page" href="#Home">Home</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="#About">About</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="#Features">Features</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="#Products">Products</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="#Frequently_Questions">FAQ</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{route('terms-condition')}}">Terms and Conditions</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="container overlaping_div" id="Download_keepr">
                                <div class="row" id="Overlap-content">
                                    <div class="col-lg-5 col-md-5 col-sm-12 overlaping_div_content">
                                        <h1 class="abs_div_content">
                                            Download the keepr Mobile App For free
                                        </h1>
                                        <p class="mt-4">Download our latest version and please don’t <br> forget to rate
                                        </p>
                                        <div class="app_download_btn">
                                            <img src="{{ asset('keepr_app_assets/assests/PlayStore_Icon.svg') }}" alt="">
                                            <img src="{{ asset('keepr_app_assets/assests/AppStore_Icon.svg') }}" alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-7 col-md-7 col-sm-12 text-end">
                                        <img src="{{ asset('keepr_app_assets/assests/TriMobile.png')}}" alt="">

                                    </div>
                                </div>
                            </div>

                            <div class="Copyright_Section pt-4 pb-4">
                                <div class="CopyRight_Section">
                                    <p class="text-light">&#9400; Copyright 2023 Keepr, All Rights Reserved.</p>
                                </div>
                                <div class="CopyRight_Section">

                                    <img class="mx-1" src="{{ asset('keepr_app_assets/assests/FaceBook_Icon.svg') }}" alt="">
                                    <img class="mx-1" src="{{ asset('keepr_app_assets/assests/TwitterIcon.svg') }}" alt="">
                                    <img class="mx-1" src="{{ asset('keepr_app_assets/assests/InstgramIcon.svg') }}" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                        <button type="button" class="close border-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <iframe class="Youtube_Video" width="560" height="315" src="https://www.youtube.com/embed/D0UnqGm_miA" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
</body>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $(".desc-1").hover(function() {
            $(this).addClass("active");
            $(".desc-2").removeClass("active");
            $(".desc-3").removeClass("active");
            $(".desc-4").removeClass("active");
        })
        $(".desc-2").hover(function() {
            $(this).addClass("active");
            $(".desc-1").removeClass("active");
            $(".desc-3").removeClass("active");
            $(".desc-4").removeClass("active");
        })
        $(".desc-3").hover(function() {
            $(this).addClass("active");
            $(".desc-2").removeClass("active");
            $(".desc-1").removeClass("active");
            $(".desc-4").removeClass("active");
        });
    });
</script>

</html>