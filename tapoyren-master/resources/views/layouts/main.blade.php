<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description"
            content="TapÖyrən is an online learning platform that helps anyone achieve their personal and professional goals" />
        <title>@yield('title',config('app.name'))</title>

        <!-- Facebook Pixel Code -->
        <script>
            ! function (f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function () {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window, document, 'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '2610528279203849');
            fbq('track', 'PageView');
        </script>

        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (m, e, t, r, i, k, a) {
                m[i] = m[i] || function () {
                    (m[i].a = m[i].a || []).push(arguments)
                };
                m[i].l = 1 * new Date();
                k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
            })
                (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(62121247, "init", {
                clickmap: true,
                trackLinks: true,
                accurateTrackBounce: true,
                webvisor: true
            });
        </script>
        <noscript>
            <div><img src="https://mc.yandex.ru/watch/62121247" style="position:absolute; left:-9999px;" alt="" /></div>
        </noscript>
        <!-- /Yandex.Metrika counter -->


        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=2610528279203849&ev=PageView&noscript=1" /></noscript>
        <!-- End Facebook Pixel Code -->

        <noscript>
            <div><img src="https://mc.yandex.ru/watch/62121247" style="position:absolute; left:-9999px;" alt="" /></div>
        </noscript>
        <!-- /Yandex.Metrika counter -->

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

        <link rel="stylesheet" href="{{ asset('public/css/app.css') }}">
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-143266485-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-143266485-1');
        </script>
        @stack('head')
    </head>

    <body>

        <div class="preloader">
            <div class="sk-double-bounce">
                <div class="sk-child sk-double-bounce1"></div>
                <div class="sk-child sk-double-bounce2"></div>
            </div>
        </div>

        @yield('content')

        @if(app()->environment()==='local')
        <script src="http://localhost:3000/browser-sync/browser-sync-client.js?v=2.26.7"></script>
        @endif

        <!-- jQuery -->
        <script src="{{ asset('public/theme/vendor/jquery.min.js') }}"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

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

        @if(session()->has('message_warning'))
        <script type="text/javascript">
            Swal.fire(
                'Warning!',
                "{{ session()->get('message_warning') }}",
                'warning'
            )
        </script>
        @endif

        @stack('footer')

    </body>

</html>
