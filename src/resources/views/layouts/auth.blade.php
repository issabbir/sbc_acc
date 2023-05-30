<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta name="author" content="CNS LIMITED">
	<title>{{--Financial Accounting System (FAS)--}}{{env('MODULE_TITLE')}} @yield('title')</title>
	<link rel="apple-touch-icon" href="{{asset('assets/images/ico/apple-icon-120.html')}}">
	<link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/images/logo/sbc_favicon.png')}}">
	<link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,600%7CIBM+Plex+Sans:300,400,500,600,700" rel="stylesheet">

	<!-- BEGIN: Vendor CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/vendors.min.css')}}">
	<!-- END: Vendor CSS-->
    <style>
        @font-face {
            font-family: boxicons;
            font-weight: 400;
            font-style: normal;
            src: url({{asset('assets/fonts/boxicons/fonts/boxicons.eot')}});
            src: url({{asset('assets/fonts/boxicons/fonts/boxicons.eot')}}) format('embedded-opentype'), url({{asset('assets/fonts/boxicons/fonts/boxicons.woff2')}}) format('woff2'), url({{asset('assets/fonts/boxicons/fonts/boxicons.woff')}}) format('woff'), url({{asset('assets/fonts/boxicons/fonts/boxicons.ttf')}}) format('truetype'), url({{asset('assets/fonts/boxicons/fonts/boxicons.svg?#boxicons')}}) format('svg');
        }
    </style>

	<!-- BEGIN: Theme CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap-extended.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/colors.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/components.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/themes/dark-layout.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/themes/semi-dark-layout.min.css')}}">
	<!-- END: Theme CSS-->

	<!-- BEGIN: Page CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/core/menu/menu-types/vertical-menu.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/pages/authentication.css')}}">
	<!-- END: Page CSS-->

	<!-- BEGIN: Custom CSS-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">

	<style>

		/*.bg-rgba-cblack{
			background: rgba(11, 44, 137, 0.59);
		}
		.bg-rgba-cwhite {
			background-color: rgba(193, 210, 201, 0.51);
		}*/

	</style>
	<!-- END: Custom CSS-->

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->
{{--
<body class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" style="background: url({{asset('/assets/images/pages/login-bg.jpg')}}) center center no-repeat; background-size: cover;">
--}}
<body class="vertical-layout vertical-menu-modern 1-column  navbar-sticky footer-static blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column" {{--style="background: url({{asset('/assets/images/pages/background.jpg')}}) center center no-repeat; background-size: cover;"--}}>
{{--<div class="se-pre-con"></div>--}}
<!-- BEGIN: Content-->
<div class="app-content content">
	<div class="content-wrapper">
		@yield('content')
	</div>
</div>
<!-- END: Content-->


<!-- BEGIN: Vendor JS-->
<script src="{{asset('assets/vendors/js/vendors.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js')}}"></script>
<!-- BEGIN Vendor JS-->

<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.tools.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.defaults.min.js')}}"></script>
<script src="{{asset('assets/fonts/LivIconsEvo/js/LivIconsEvo.min.js')}}"></script>

<!-- BEGIN: Page Vendor JS-->
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{asset('assets/js/scripts/configs/vertical-menu-light.min.js')}}"></script>
<script src="{{asset('assets/js/core/app-menu.min.js')}}"></script>
<script src="{{asset('assets/js/core/app.min.js')}}"></script>
<script src="{{asset('assets/js/scripts/components.min.js')}}"></script>
<script src="{{asset('assets/js/scripts/footer.min.js')}}"></script>

{{--<script>
    $(window).on('load', function() {
        // Animate loader off screen
        $(".se-pre-con").fadeOut("slow");;
    });
</script>--}}
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<!-- END: Page JS-->


<style>
    html body.blank-page .content-wrapper .flexbox-container{
        height: 96vh!important;
    }
    html body {
        height: 100%;
        background-color: #fff!important;
        direction: ltr;
    }
    #login_container{
        background-position:center;
        /*padding: 13px;*/
        background: url('/assets/images/logo/login_bg_sbc.png');
        background-repeat: no-repeat;
        background-origin: content-box;
        background-size:contain ;
        min-height:530px
    }
    #login_container form .alert-danger button {
        width: 14% !important;
        background: unset !important;
    }
    #login_container fa fa-eye-slash{
        margin-top: 5px;
    }
    #login_container .container{
        padding: 20px;
    }
    #login_container form{
        /*border: 1px solid red;*/
        padding: 15px 27px;
    }
    #login_container form button{
        color: #fff;
    }
    #login_container form button{
        width: 70%!important;
        background: #001862;
        color: #fff;
    }
    #login_container form .card-title{
        margin-bottom:0px !important;
    }
    #login_container form .card-header .card-title h4 strong{
        border-bottom: 3px solid red;
        color: #001862;
    }
    #login_container form .form-control{
        width: 70%;
    }

    #login_container form small{
        color: #5f5f61;
        font-size: 100%;
    }
    #login_container form label {
        color: #5f5f61;
    }
    /*.mtop_3{
        margin-top: 3rem;
    }*/

    .form-control:focus{
        margin-top: 5px;
    }
    #login_container #input-password {
        width: 70%;
    }
    #login_container #input-password .form-control {
        width: 100%;
    }
    #login_container #input-password .fa{
        color: #2c2626;
    }
    /*@media screen and (max-width: 1024px) {*/
    /*    #login_container form .form-control {*/
    /*        width: 70%;*/
    /*    }*/
    /*    .card-body{*/
    /*        padding: 1rem !important;*/
    /*    }*/
    /*    .card-footer, .card-header{*/
    /*        padding: 0rem 1.7rem !important;*/
    /*    }*/
    /*}*/
    #login_logo {
        display: none;
    }
    @media screen and (min-width:1200px) {
        .mtop_3{
            margin-top: 2rem !important;
        }
    }
    @media screen and (min-width: 1366px) and (max-width: 1440px) {
        #login_container form .form-control {
            width: 70%;
        }
        .card-body{
            padding: 1rem !important;
        }
        .card-footer, .card-header{
            padding: 0rem 1.7rem !important;
        }
        .card-content .card-body{
            padding: 1rem !important;
        }
        /*.mtop_3{
            margin-top: 0rem !important;
        }*/
    }
    @media screen and (max-width: 1336px) {
        #login_container form .form-control {
            width: 70%;
        }
        .card-body{
            padding: 1rem !important;
        }
        .card-footer, .card-header{
            padding: 0rem 1.7rem !important;
        }
        .card-content .card-body{
            padding: 1rem !important;
        }
        /*.mtop_3{
            margin-top: 0rem !important;
        }*/
    }
    @media screen and (max-width: 1024px) {
        #login_container {
            min-height: 368px;
        }
        #login_container form .form-control {
            font-size: 10px;
            width: 70%;
        }
        #login_container .card-body{
            padding: 1rem !important;
        }
        #login_container .card-footer, .card-header{
            padding: 0rem .7rem !important;
        }
        #login_container  .card-content .card-body{
            padding: .5rem !important;
        }
        #login_container .mtop_3{
            margin-top: .5rem !important;
        }
        #login_container .form-group {
            margin-bottom: 0.3rem;
        }
        #login_container form {
            padding: 2px 27px;
        }
        #login_container form button {
            width: 70%!important;
            background: #001862;
            color: #fff;
        }
        .mb-1, .my-1 {
            margin-bottom: -0.5rem!important;
        }
    }

    @media screen and (max-width: 991px) {
        .col-md-8{
            -webkit-box-flex: 0;
            -webkit-flex: 0 0 100%;
            -ms-flex: 0 0 100%;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
    }

    @media screen and (max-width: 480px) {
        #login_container{
            background-position:center;
            background: unset;
            background-repeat: no-repeat;
            background-origin: content-box;
            background-size:contain ;
            min-height:auto;
            background-color: #f8f8f8;
            padding: 30px 0px;
        }
        #login_container form small{
            font-size: 97%;
        }
        #login_container #input-password{
            width: 100%;
        }
        #login_container form .form-control{
            width: 100%;
        }
        #login_container form .card-title {
            text-align: center;
        }
        #login_container form button{
            width: 100% !important;
        }

        #login_container .mtop_3 {
            margin-top: 1.5rem !important;
        }
        #login_logo {
            display: inline;
        }
    }
    @media screen and (max-width: 540px) {
        #login_container{
            background-position:center;
            background: unset;
            background-repeat: no-repeat;
            background-origin: content-box;
            background-size:contain ;
            min-height:auto;
            background-color: #f8f8f8;
            padding: 30px 0px;
        }
        #login_container form small{
            font-size: 97%;
        }
        #login_container #input-password{
            width: 100%;
        }
        #login_container form .form-control{
            width: 100%;
        }
        #login_container form .card-title {
            text-align: center;
        }
        #login_container form button{
            width: 100% !important;
        }

        #login_container .mtop_3 {
            margin-top: 1.5rem !important;
        }
        #login_logo {
            display: inline;
        }
    }
    @media screen and (min-width: 1366px){
        #login_container form .alert-danger{
            width:70% !important;
        }
    }

    .form{width: 250px}
    .main-password {position: relative;}
    .icon-view{position: absolute; right: 12px; top: 6px;}


    /*Footer start*/
    /*.codepen_profile{position: fixed; right: 20px; bottom: 20px;}
    .codepen_profile a {background: rgb(245 122 32 / 53%); padding: 15px; border-radius: 50%; box-shadow: hsl(0deg 0% 80%) 0 5px 16px; height: 60px; width: 60px; display: inline-block; }*/
</style>

</body>
<!-- END: Body-->

</html>
