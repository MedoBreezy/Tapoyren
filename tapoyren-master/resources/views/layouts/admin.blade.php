<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <title>@yield('title',config('app.name'))</title>

        <!-- Perfect Scrollbar -->
        <link type="text/css" href="{{ asset('public/theme/vendor/perfect-scrollbar.css') }}" rel="stylesheet">

        <!-- Fix Footer CSS -->
        <link type="text/css" href="{{ asset('public/theme/vendor/fix-footer.css') }}" rel="stylesheet">

        <!-- Material Design Icons -->
        <link type="text/css" href="{{ asset('public/theme/css/material-icons.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('public/theme/css/material-icons.rtl.css') }}" rel="stylesheet">

        <!-- Font Awesome Icons -->
        <link type="text/css" href="{{ asset('public/theme/css/fontawesome.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('public/theme/css/fontawesome.rtl.css') }}" rel="stylesheet">

        <!-- Preloader -->
        <link type="text/css" href="{{ asset('public/theme/css/preloader.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('public/theme/css/preloader.rtl.css') }}" rel="stylesheet">

        <!-- App CSS -->
        <link type="text/css" href="{{ asset('public/theme/css/app.css') }}" rel="stylesheet">
        <link type="text/css" href="{{ asset('public/theme/css/app.rtl.css') }}" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('public/css/admin.css') }}">
        @stack('head')
    </head>

    <body>

        <div class="preloader">
            <div class="sk-double-bounce">
                <div class="sk-child sk-double-bounce1"></div>
                <div class="sk-child sk-double-bounce2"></div>
            </div>
        </div>

        <div class="mdk-header-layout js-mdk-header-layout">

            <div class="mdk-header-layout__content page-content">
                <div class="admin-content">
                    @include('admin.parts.sidebar')
                    <div class="admin-page">
                        @yield('content')
                    </div>
                </div>


            </div>

        </div>

        @if(app()->environment()==='local')
        <script src="http://localhost:3000/browser-sync/browser-sync-client.js?v=2.26.7"></script>
        @endif

        <!-- jQuery -->
        <script src="{{ asset('public/theme/vendor/jquery.min.js') }}"></script>

        <!-- Bootstrap -->
        <script src="{{ asset('public/theme/vendor/popper.min.js') }}"></script>
        <script src="{{ asset('public/theme/vendor/bootstrap.min.js') }}"></script>

        <!-- Perfect Scrollbar -->
        <script src="{{ asset('public/theme/vendor/perfect-scrollbar.min.js') }}"></script>

        <!-- DOM Factory -->
        <script src="{{ asset('public/theme/vendor/dom-factory.js') }}"></script>

        <!-- MDK -->
        <script src="{{ asset('public/theme/vendor/material-design-kit.js') }}"></script>

        <!-- Fix Footer -->
        <script src="{{ asset('public/theme/vendor/fix-footer.js') }}"></script>

        <!-- Chart.js -->
        <script src="{{ asset('public/theme/vendor/Chart.min.js') }}"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

        <!-- App JS -->
        <script src="{{ asset('public/theme/js/app.js') }}"></script>

        <!-- Highlight.js -->
        <script src="{{ asset('public/theme/js/hljs.js') }}"></script>

        <!-- App Settings (safe to remove) -->
        <script src="{{ asset('public/theme/js/app-settings.js') }}"></script>

        <script src="{{ asset('public/js/app.js') }}"></script>

        @if(session()->has('message_success'))
        <script type="text/javascript">
            Swal.fire(
                'Success!',
                "{{ session()->get('message_success') }}",
                'success'
            )
        </script>
        @endif

        @stack('footer')

    </body>

</html>
