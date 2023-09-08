<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keepr App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('keepr_app_assets/style.css') }}">
    <link rel="icon" type="image/x-icon" href="{{asset("public/company/keepr-favicon.png")}}">
</head>

<body>
    <div>
        <div class="Navbar-Section">
            <nav class="navbar container navbar-expand-lg navbar-light pb-3 pt-0">
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
                                <a class="nav-link" aria-current="page" href="{{route('home')}}">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('home')}}/#About">About</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('home')}}/#Features">Features</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('home')}}/#Products">Products</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{route('home')}}/#Frequently_Questions">FAQ</a>
                            </li>
                        </ul>
                        <form class="d-flex mt-3">
                            <a href="{{route('home')}}/#downloads" class="btn download_keeper_btn" type="submit">Download Keepr</a>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div class="container-fluid" style="padding-top: 80px;">
        <div class="container-fluid" id="Terms_And_Condition">
            <div class="container" id="Terms_And_Condition_main">
                <h2 class="Heading_terms" style="text-align: center;">TERMS AND CONDITIONS</h2>
                {!! $data ?? "" !!}
                <!-- <ol>
                    <li><strong>Introduction</strong></li>
                </ol>
                <p>The On Standard Terms and Conditions written on this webpage shall manage your use of On. These Terms will be applied fully and affect to your use of On. By using On, you agreed to accept all terms and conditions written in here. You must not use On if you disagree with any of the On Standard Terms and Conditions.</p>
                <p>Minors or people below 7 years old are not allowed to use On.</p>
                <ol start="2">
                    <li><strong>Intellectual Property Rights</strong></li>
                </ol>
                <p>Other than the content you own, under these Terms, On and/or its licensors own all the intellectual property rights and materials contained in On.</p>
                <p>You are granted limited license only for purposes of viewing the material contained on On.</p>
                <ol start="3">
                    <li><strong>Restrictions</strong></li>
                </ol>
                <p>You are specifically restricted from all of the following</p>
                <ul>
                    <li>publishing any On material in any other media;</li>
                    <li>selling, sublicensing and/or otherwise commercializing any On material;</li>
                    <li>publicly performing and/or showing any On material;</li>
                    <li>using On in any way that is or may be damaging to On;</li>
                    <li>using On in any way that impacts user access to On;</li>
                    <li>using On contrary to applicable laws and regulations, or in any way may cause harm to On, or to any person or business entity;</li>
                    <li>engaging in any data mining, data harvesting, data extracting or any other similar activity in relation to On;</li>
                    <li>using On to engage in any advertising or marketing.</li>
                </ul>
                <p>Certain areas of On are restricted from being access by you and On may further restrict access by you to any areas of On, at any time, in absolute discretion. Any user ID and password you may have for On are confidential and you must maintain confidentiality as well.</p>
                <ol start="4">
                    <li><strong>Your Content</strong></li>
                </ol>
                <p>In these On Standard Terms and Conditions, “Your Content” shall mean any audio, video text, images or other material you choose to display on On. By displaying Your Content, you grant On a non-exclusive, worldwide irrevocable, sub licensable license to use, reproduce, adapt, publish, translate and distribute it in any and all media.</p>
                <p>Your Content must be your own and must not be invading any third-party’s rights. On reserves the right to remove any of Your Content from On at any time without notice.</p>
                <ol start="5">
                    <li><strong>No warranties</strong></li>
                </ol>
                <p>On is provided “as is,” with all faults, and On express no representations or warranties, of any kind related to On or the materials contained on On. Also, nothing contained on On shall be interpreted as advising you.</p>
                <ol start="6">
                    <li><strong>Limitation of liability</strong></li>
                </ol>
                <p>In no event shall On, nor any of its officers, directors and employees, shall be held liable for anything arising out of or in any way connected with your use of On whether such liability is under contract. &nbsp;On, including its officers, directors and employees shall not be held liable for any indirect, consequential or special liability arising out of or in any way related to your use of On.</p>
                <ol start="7">
                    <li><strong>Indemnification</strong></li>
                </ol>
                <p>You hereby indemnify to the fullest extent On from and against any and/or all liabilities, costs, demands, causes of action, damages and expenses arising in any way related to your breach of any of the provisions of these Terms.</p>
                <ol start="8">
                    <li><strong>Severability</strong></li>
                </ol>
                <p>If any provision of these Terms is found to be invalid under any applicable law, such provisions shall be deleted without affecting the remaining provisions herein.</p>
                <ol start="9">
                    <li><strong>Variation of Terms</strong></li>
                </ol>
                <p>On is permitted to revise these Terms at any time as it sees fit, and by using On you are expected to review these Terms on a regular basis.</p>
                <ol start="10">
                    <li><strong>Assignment</strong></li>
                </ol>
                <p>On is allowed to assign, transfer, and subcontract its rights and/or obligations under these Terms without any notification. However, you are not allowed to assign, transfer, or subcontract any of your rights and/or obligations under these Terms.</p>
                <ol start="11">
                    <li><strong>Entire Agreement</strong></li>
                </ol>
                <p>These Terms constitute the entire agreement between On and you in relation to your use of On, and supersede all prior agreements and understandings.</p>
                <ol start="12">
                    <li><strong>Governing Law &amp; Jurisdiction</strong></li>
                </ol> -->

            </div>
        </div>

        <div class="container-fluid Fifth_Section" id="Fifth_Section">
            <div class="container-fluid footer">
                <div class="footer_content">
                    <div class="container">
                        <div class="row" style="position: relative;">
                            <div class="col-md-12 footer-body" id="Terms_conditionn_footer">

                                <img src="{{ asset('keepr_app_assets/assests/Footer_Log.svg') }}" alt="">

                            </div>
                            <div class="footer-body2">
                                <div class="text-center">
                                    <ul class="footer_menu justify-content-center">
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" aria-current="page" href="{{route('home')}}/#Home">Home</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{route('home')}}/#About">About</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{route('home')}}/#Features">Features</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{route('home')}}/#Products">Products</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="{{route('home')}}/#Frequently_Questions">FAQ</a>
                                        </li>
                                        <li class="nav-item mx-3">
                                            <a class="nav-link" href="#">Terms and Conditions</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>


                            <div class="Copyright_Section pb-4 mt-3">
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




</body>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

</html>