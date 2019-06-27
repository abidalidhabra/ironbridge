<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title','ironbridge1779 | Admin Panel')</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content=""/>

    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="{{ asset('admin_assets/favicon/apple-touch-icon-57x57.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{ asset('admin_assets/favicon/apple-touch-icon-114x114.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{ asset('admin_assets/favicon/apple-touch-icon-72x72.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{ asset('admin_assets/favicon/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="60x60" href="{{ asset('admin_assets/favicon/apple-touch-icon-60x60.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="{{ asset('admin_assets/favicon/apple-touch-icon-120x120.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="{{ asset('admin_assets/favicon/apple-touch-icon-76x76.png') }}" />
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="{{ asset('admin_assets/favicon/apple-touch-icon-152x152.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/favicon/favicon-196x196.png') }}" sizes="196x196" />
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/favicon/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/favicon/favicon-32x32.png') }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/favicon/favicon-16x16.png') }}" sizes="16x16" />
    <link rel="icon" type="image/png" href="{{ asset('admin_assets/favicon/favicon-128.png') }}" sizes="128x128" />

    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/css/datatables.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin_assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/jquery.fancybox.min.css') }}" />


    <script src="{{ asset('admin_assets/js/jquery.js') }}"></script>
    <script src="{{ asset('admin_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/datatables.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('admin_assets/js/toastr.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/bootstrap-confirmation.min.js') }}"></script>
    <script src="{{ asset('admin_assets/js/jquery.fancybox.min.js') }}"></script>
    @yield('styles')
</head>

<body>  
        @if(Auth::guard('admin')->check())
            @include('admin.layouts.header')
            <div class="parettow_coverbox">
                @include('admin.layouts.left-sidebar')
                @yield('content')
            </div>
        @else
            @yield('content')
        @endif
</body>

@yield('scripts')
<script>
    // Off Canvas Menu Open & Close
    $('#offCanvas').on('click', function () {
        $('.nav-offcanvas').addClass('open');
        $('.offcanvas-overlay').addClass('on');
    });
    $('#offCanvasClose, .offcanvas-overlay').on('click', function () {
        $('.nav-offcanvas').removeClass('open');
        $('.offcanvas-overlay').removeClass('on');
    });

</script>
</html>
