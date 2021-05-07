<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>@yield('title',config('app.name'))</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="{{ asset('public/dashboard/images/favicon.ico') }}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('public/dashboard/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet"
            type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('public/dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('public/dashboard/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
        <link href="{{ asset('public/design/dashboard.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
        <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
        <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
        <style>
            .anychart-credits {
                display: none !important;
            }
        </style>
        @stack('header')
    </head>

    <body data-layout="detached" data-topbar="colored">

        <div class="container-fluid">

            <div id="layout-wrapper">

                @include('layouts.dashboard.parts.header')
                @include('layouts.dashboard.parts.sidebar')



                <div class="main-content">

                    <div class="page-content">

                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-flex align-items-center justify-content-between">
                                    <h4 class="page-title mb-0 font-size-18">@yield('page_title',translate('dashboard'))
                                    </h4>
                                </div>
                            </div>
                        </div>

                        @yield('content')

                    </div>

                    <footer class="footer">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-sm-6">
                                    &copy; {{ date('Y') }}, {{ config('app.name') }}
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>

            </div>

        </div>

        @if(app()->environment()==='local')
        <script src="http://localhost:3000/browser-sync/browser-sync-client.js?v=2.26.7"></script>
        @endif

        <!-- JAVASCRIPT -->
        <script src="{{ asset('public/dashboard/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('public/dashboard/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('public/dashboard/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('public/dashboard/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('public/dashboard/libs/node-waves/waves.min.js') }}"></script>

        <!-- apexcharts -->
        <script src="{{ asset('public/dashboard/libs/apexcharts/apexcharts.min.js') }}"></script>

        <script src="{{ asset('public/dashboard/js/app.js') }}"></script>

        @stack('footer')

    </body>

</html>
