<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title','ironbridge1779 | Admin Panel')</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content=""/>
    <link rel="stylesheet" href="{{ asset('admin_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_assets/css/datatables.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('admin_assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin_assets/css/toastr.min.css') }}">



    <script type="text/javascript" src="{{ asset('admin_assets/js/jquery.js') }}"></script>
    <script src="{{ asset('admin_assets/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin_assets/js/datatables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin_assets/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin_assets/js/toastr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin_assets/js/jquery.validate.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('admin_assets/js/bootstrap-confirmation.min.js') }}"></script>
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

</html>
