<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keepr App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('keepr_app_assets/style.css') }}">
    <link rel="icon" type="image/x-icon" href="{{asset("public/company/Keepr-logo-black.png")}}">
</head>

<body>
    <div class="Navbar-Section">
        <nav class="navbar container navbar-expand-lg navbar-light pb-3 pt-0">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><img src="{{ asset('keepr_app_assets/assests/Keepe_logo.png') }}" alt=""></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                    aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarScroll">
                    <ul class="navbar-nav me-auto m-auto my-2 my-lg-0 navbar-nav-scroll"
                        style="--bs-scroll-height: 100px;">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#home">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#features">Features</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#products">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#faq">FAQ</a>
                        </li>
                    </ul>
                    <a href="#download_keepr" class="btn download_keeper_btn" >Download Keepr</a>
                </div>
            </div>
        </nav>
    </div>

    <div class="container-fluid" style="padding-top: 80px;">
        <div class="section_seocond">
            <div class="For_picture" id="home">
                <div class="container">
                    <div class="row h-100">
                        <div class="leftKeeprPic col-md-6">
                            <div class="Main_Content">
                                <span class="main_heading">Keep your valuable stuff always with you</span>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                    incididunt
                                    ut
                                    labore et dolore magna aliqua. Ut enim ad minim veniam,</p>
                                <div class="download_app_btn">
                                    <a href="#download_keepr" class="btn" type="button ">Download keepr</a>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="rightMobileIcon">
                                <img src="{{ asset('keepr_app_assets/assests/mobileicon_x2.png') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="container-fluid section_three">
            <div class="container">
                <div class="About_seocond row">
                    <div class="col-md-6">
                        <div class="rightMobileIcon2">
                            <img src="{{ asset('keepr_app_assets/assests/About2.png') }}" alt="">
                        </div>
                    </div>
                    <div class="leftKeeprPic col-md-6" id="about">
                        <div class="ms-1 About_Content">About</div>
                        <div class="About_Main_Content">
                            <span class="Ultimate_Bluetooth_Finder">Keepr Ultimate Bluetooth Finder</span>
                            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat
                                nulla
                                pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia anim
                                id
                                est
                                laborum.
                            </p>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut
                                labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
                                ullamco
                                laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                            <div class="download_app_btn_About">
                                <a href="" class="btn" type="button ">Download keepr</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container step_section_row">
                    <div class="step_section row">
                        <div class="col-md-4 steps">
                            <div>
                                <div class="d-flex">
                                    <div class="step-circle">
                                        1
                                    </div>
                                    <div class="Steps_Content">
                                        Easy buy your own beacon
                                    </div>
                                </div>
                                <p class="ms-5 mt-3">Duis aute irure dolor in reprehein voluptate velit esse
                                </p>
                            </div>
                            <div class="arrow-icon">
                                <img src="{{ asset('keepr_app_assets/assests/Group 14709.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 steps">
                            <div>
                                <div class="d-flex">
                                    <div class="step-circle">
                                        2
                                    </div>
                                    <div class="Steps_Content">
                                        Easy buy your own beacon
                                    </div>
                                </div>
                                <p class="ms-5 mt-3">Duis aute irure dolor in reprehein voluptate velit esse
                                </p>
                            </div>
                            <div class="arrow-icon">
                                <img src="{{ asset('keepr_app_assets/assests/Group 14709.svg') }}" alt="">
                            </div>
                        </div>
                        <div class="col-md-4 steps">
                            <div>
                                <div class="d-flex">
                                    <div class="step-circle">
                                        3
                                    </div>
                                    <div class="Steps_Content">
                                        Easy buy your own beacon
                                    </div>
                                </div>
                                <p class="ms-5 mt-3">Duis aute irure dolor in reprehein voluptate velit esse
                                </p>
                            </div>
                            <div class="arrow-icon">
                                <img src="{{ asset('keepr_app_assets/assests/Group 14709.svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid Four_Section" id="features">
            <div class="container">
                <div class="Unlock_Next_Level "> Unlock Next-Level Amazing Features!</div>
                <div class="row">
                    <!-- <div class="col-md-12 col-lg-1 col-xl-1"></div> -->
                    <div class="col-md-6 pt-5 ">
                        <div class="desc-1 active p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                        <div class="desc-2 pt-3 p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                        <div class="desc-3 pt-3 p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                        <div class="desc-4 pt-3 p-3">
                            <h5>Easy to use and implement</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                    </div>
                    <div class="col-md-6 pt-4 mobile-icon">
                        <img id="AboutImage" src="{{ asset('keepr_app_assets/assests/25.png') }}" alt="">
                    </div>
                </div>

            </div>
        </div>


        <div class="container-fluid">
            <div class="overlay">
                <div class="overlay-image">
                    <img src="{{ asset('keepr_app_assets/assests/Group 14723.png') }}" class="video_Play_Button" alt="video_Play_Button">
                </div>
            </div>
        </div>


        <div class="container-fluid Fifth_Section">
            <div class="container" id="products">
                <h1 class="text-center" style="font-weight: 600;">Our Products</h1>
                <div class="row justify-content-center mt-5">
                    <div class="col-md-12 col-lg-5 col-xl-5 Product-1 mx-1">
                        <img src="{{ asset('keepr_app_assets/assests/Group 14279.png') }}" alt="">
                        <h4 class="ibeacon-headline">Keepr 60m Ibeacon device BLE</h4>
                        <p class="ibeacon_peragraph">Sed ut periciatis unde omnis iste natus error sit volatem
                            accusantium laudantium.</p>
                        <a href="" class="btn buy_now_btn" type="button">Buy Now</a>
                    </div>
                    <div class="col-md-12 col-lg-5 col-xl-5 mt-md-4 mt-sm-4 mt-lg-0 Product-2 mx-1">
                        <img src="{{ asset('keepr_app_assets/assests/Group 14279.png') }}" alt="">
                        <h4 class="ibeacon-headline">Keepr14580 80m BLE</h4>
                        <p class="ibeacon_peragraph">Sed ut periciatis unde omnis iste natus error sit volatem
                            accusantium laudantium.</p>
                        <a href="" class="btn buy_now_btn" type="button">Buy Now</a>
                    </div>
                </div>
            </div>
            <div class="container mt-lg-5 pt-lg-5 mt-md-5 pt-md-5 Frequently_Questions" id="faq">
                <h1 class="text-center" style="font-weight: 600">Frequently Asked Questions</h1>
                <div class="row justify-content-center mt-5">
                    <div class="col-md-6 Frequently_Asked_Questions">
                        <div class="">
                            <h5>How to install Keepr?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                        <div class="pt-3">
                            <h5>How can I edit my personal information?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                                architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem</p>
                        </div>
                        <div class="pt-3">
                            <h5>Do you have a free trail?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam.</p>
                        </div>
                        <div class="pt-3">
                            <h5>Nemo enim ipsam voluptatem</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                    </div>
                    <div class="col-md-6 Frequently_Asked_Questions">
                        <div>
                            <h5>Do you have a free trail?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam.</p>
                        </div>
                        <div class="pt-3">
                            <h5>Do you have a free trail?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis</p>
                        </div>
                        <div class="pt-3">
                            <h5>How can I edit my personal information?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
                                architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem.</p>
                        </div>
                        <div class="pt-3">
                            <h5>Do you have a free trail?</h5>
                            <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accus antium doloremque
                                laudantium, totam.</p>
                        </div>
                    </div>
                </div>
                <h6 class="text-center mt-4 mb-5">
                    <b>Haven't find suitable answer? </b><b class="tellUs">Tell us what you need</b>
                </h6>
            </div>
        </div>

        <div class="container-fluid Seven_Section">
            <div class="container">
                <div class="row">
                    <!-- <div class="col-md-1"></div> -->
                    <div class="col-md-6 rightSection">
                        <div>
                            <h3 class="More_Questions">Have more questions? Don’t hesitate to reach us</h3>
                            <div class="Seven_Section_Social">
                                <div class="mail_section">
                                    <img src="{{ asset('keepr_app_assets/assests/Group 14740.svg') }}" alt=""> <a href="mailto:support@keepr.com">
                                        &nbsp; support@keepr.com</a>
                                </div>
                                <div class="mail_section">
                                    <img src="{{ asset('keepr_app_assets/assests/Group 14741.svg') }}" alt=""> <span> &nbsp; +1 987 6543 210</span>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- <div class="col-md-1"></div> -->
                    <div class="col-md-6 HeadPhone_image">
                        <img src="{{ asset('keepr_app_assets/assests/Group 14739.svg') }}" alt="">
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid footer">
            <div class="footer_content">
                <div class="container">
                    <div class="row" style="position: relative;">
                        <div class="col-md-12 footer-body">
                            <img src="{{ asset('keepr_app_assets/assests/Group 14743.svg') }}" alt="">
                        </div>
                        <div class="footer-body2">
                            <div class="text-center">
                                <ul class="footer_menu row">
                                    <li class="nav-item col">
                                        <a class="nav-link active" aria-current="page" href="#home">Home</a>
                                    </li>
                                    <li class="nav-item col">
                                        <a class="nav-link" href="#about">About</a>
                                    </li>
                                    <li class="nav-item col">
                                        <a class="nav-link" href="#features">Features</a>
                                    </li>
                                    <li class="nav-item col">
                                        <a class="nav-link" href="#products">Products</a>
                                    </li>
                                    <li class="nav-item col">
                                        <a class="nav-link" href="#faq">FAQ</a>
                                    </li>
                                    <li class="nav-item col">
                                        <a class="nav-link" href="#">Terms and Conditions</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="container overlaping_div" id="download_keepr">
                            <div class="row">
                                <div class="col-lg-5 col-md-5 col-sm-12 overlaping_div_content">
                                    <h1 class="abs_div_content">
                                        Let’s Download Keepr Mobile App Free
                                    </h1>
                                    <p class="mt-4">Download our latest version and please don’t <br> forget to rate</p>
                                    <div class="app_download_btn">
                                        <img src="{{ asset('keepr_app_assets/assests/Group 14720.svg') }}" alt="">
                                        <img src="{{ asset('keepr_app_assets/assests/Group 14721.svg') }}" alt="">
                                    </div>
                                </div>
                                <div class="col-lg-7 col-md-7 col-sm-12 text-end">
                                    <img src="{{ asset('keepr_app_assets/assests/Group 14763.png') }}" alt="">
                                </div>
                            </div>
                        </div>

                        <div class="Copyright_Section pt-4 pb-4">
                            <div class="CopyRight_Section">
                                <p class="text-light">&copy;Copyright 2023 Keepr, All Rights Reserved.</p>
                            </div>
                            <div class="CopyRight_Section">
                                <img class="mx-1" src="{{ asset('keepr_app_assets/assests/Layer 2.svg') }}" alt="">
                                <img class="mx-1" src="{{ asset('keepr_app_assets/assests/Layer (1).svg') }}" alt="">
                                <img class="mx-1" src="{{ asset('keepr_app_assets/assests/Layer (2).svg') }}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
    integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous">
    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
    integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous">
    </script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

<script>


    $(document).ready(function () {

        $(".desc-1").hover(function () {
            $(this).addClass("active");
            $(".desc-2").removeClass("active");
            $(".desc-3").removeClass("active");
            $(".desc-4").removeClass("active");
            $("#AboutImage").attr("src", "{{ asset('keepr_app_assets/assests/25.png') }}");
        })
        $(".desc-2").hover(function () {
            $(this).addClass("active");
            $(".desc-1").removeClass("active");
            $(".desc-3").removeClass("active");
            $(".desc-4").removeClass("active");
            $("#AboutImage").attr("src", "{{ asset('keepr_app_assets/assests/25.png') }}");
        })
        $(".desc-3").hover(function () {
            $(this).addClass("active");
            $(".desc-2").removeClass("active");
            $(".desc-1").removeClass("active");
            $(".desc-4").removeClass("active");
            $("#AboutImage").attr("src", "{{ asset('keepr_app_assets/assests/25.png') }}");
        })
        $(".desc-4").hover(function () {
            $(this).addClass("active");
            $(".desc-2").removeClass("active");
            $(".desc-3").removeClass("active");
            $(".desc-1").removeClass("active");
            $("#AboutImage").attr("src", "{{ asset('keepr_app_assets/assests/25.png') }}");
        })
    })

</script>

</html>