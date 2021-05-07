<!DOCTYPE html>
<html lang="en" dir="ltr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="description"
            content="TapÖyrən is an online learning platform that helps anyone achieve their personal and professional goals" />
        <title>@yield('title',config('app.name'))</title>

        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || []; w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                }); var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-MN7VG65');</script>
        <!-- End Google Tag Manager -->

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

        <link type="text/css" href="{{ asset('public/theme/css/material-icons.css') }}" rel="stylesheet">

        <link type="text/css" href="{{ asset('public/theme/css/fontawesome.css') }}" rel="stylesheet">


        <link rel="stylesheet" href="{{ asset('public/design/dp.css') }}">
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
        <!-- Global site tag (gtag.js) - Google Ads: 729581364 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-729581364"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());

            gtag('config', 'AW-729581364');
        </script>
        @stack('head')
    </head>

    <body>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MN7VG65" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <div class="modal-backdrop"></div>

        <div class="mobile-sidebar">

            <a href="#" class="toggle" onclick="showSidebar()">
                <i class="material-icons">menu</i>
            </a>

            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('public/design/assets/logo-blue.png') }}" />
            </a>

            @if(!auth()->check())
            <a href="{{ url('login') }}" class="sidebar-link">
                @tr('login')
            </a>
            <a href="{{ url('register/student') }}" class="sidebar-link">
                @tr('register')
            </a>
            @else
            @if(auth()->user()->type==='admin')
            <a class="sidebar-link" href="{{ url('admin') }}">@tr('dashboard')</a>
            @endif
            @if(auth()->user()->type==='student')
            <a class="sidebar-link" href="{{ url('student/dashboard') }}">@tr('dashboard')</a>
            @endif
            @if(auth()->user()->hasCompany())
            <a href="{{ url('company/dashboard') }}">@tr('company_dashboard')</a>
            @endif
            @if(auth()->user()->type==='instructor')
            <a class="sidebar-link" href="{{ url('instructor/dashboard') }}">@tr('dashboard')</a>
            @endif
            <a class="sidebar-link" href="{{ url('account/wishlist') }}">@tr('wishlist')</a>
            <a class="sidebar-link" href="{{ url('account/courses') }}">@tr('courses')</a>
            <a class="sidebar-link" href="{{ url('account/profile') }}">@tr('profile')</a>
            <a href="{{ url('logout') }}" class="sidebar-link">
                @tr('logout')
            </a>
            @endif
            <hr />
            <a class="sidebar-link" href="#" onclick="showCategories();">
                <img src="{{ asset('public/design/assets/courses.png') }}" class="icon" />
                <span>@tr('courses')</span>
            </a>
            <a class="sidebar-link" href="{{ url('about') }}">@tr('about')</a>
            <a class="sidebar-link" href="{{ url('faq') }}">@tr('faq')</a>
            <a class="sidebar-link" href="{{ url('contact') }}">@tr('contact')</a>

            <div class="language-mobile">
                @foreach(App\Language::where('slug','!=',app()->getLocale())->get() as $lang)
                <a href="{{ url('locale/'.$lang->slug) }}">{{ $lang->title }}</a>
                @endforeach
            </div>

        </div>

        <div class="mobile-search">

            <button onclick="showSearch()">
                <i class="material-icons">close</i>
            </button>

            <form class="search_wrapper" method="GET" action="{{ url('search') }}">
                <input type="text" name="query" placeholder="@tr('search_description')" />
                <button>@tr('search')</button>
            </form>

        </div>

        <div class="categories-modal">

            <div id="react-modal-categories"></div>

        </div>

        @yield('content')

        @if(app()->environment()==='local')
        <script src="http://localhost:3000/browser-sync/browser-sync-client.js?v=2.26.7"></script>
        @endif

        <script src="{{ asset('public/theme/vendor/jquery.min.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
        <script>

            document.querySelector('.modal-backdrop').addEventListener('click', () => {
                document.querySelector('.categories-modal').classList.remove('active');
                document.body.classList.remove('modal-active');
            });

            window.onclick = (e) => {

                let foundDropdown = null;

                if (e.target.dataset.toggle !== null) {
                    foundDropdown = document.getElementById(e.target.dataset.toggle);
                }

                document.querySelectorAll('.dropdown-content').forEach(dc => {
                    if (dc !== foundDropdown && !dc.contains(e.target)) dc.classList.remove('active');
                });
            };

            function showCategories() {
                document.querySelector('.categories-modal').classList.toggle('active');
                document.body.classList.toggle('modal-active');
            }
        </script>
        <script src="{{ asset('public/design/dp.js') }}"></script>
        <script>
            $('[data-toggle="datepicker"]').datepicker({
                format: 'yyyy-mm-dd'
            });
        </script>
        <script src="{{ asset('public/js/app.js') }}"></script>
        <script>
            function showSidebar() {
                document.querySelector('.mobile-sidebar').classList.toggle('active');
            }

            function showSearch() {
                document.querySelector('.mobile-search').classList.toggle('active');
            }

            $(document).ready(function () {

                const dropdowns = document.querySelectorAll('[data-toggle]');

                dropdowns.forEach(dropdown => {
                    const toggle = dropdown.dataset.toggle;
                    const dropdownWrapper = document.getElementById(toggle);

                    dropdown.addEventListener('click', function () {
                        dropdownWrapper.classList.toggle('active');
                    })


                })

                //
            });
        </script>

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
