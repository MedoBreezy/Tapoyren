<nav class="nav-desktop">
    <a href="{{ url('/') }}" class="nav-default">
        <img src="{{ asset('public/images/logo.png') }}" class="logo" />
    </a>

    <a class="nav-courses transitioned hover-opacity nav-default" href="#" onclick="showCategories();">
        <img src="{{ asset('public/design/assets/courses.png') }}" class="icon" />
        <span>@tr('courses')</span>
    </a>

    <form action="{{ url('search') }}" method="GET" class="search transitioned hover-opacity">
        <input type="text" name="query" placeholder="@tr('search_description')">
        <button>
            <i class="material-icons" style="color: #0096ff; cursor: pointer;">search</i>
        </button>
    </form>

    <div class="pages">
        <a href="{{ url('about') }}">@tr('about')</a>
        <a href="{{ url('contact') }}">@tr('contact')</a>
        <div class="separator"></div>
        @if(auth()->check())
        <a href="{{ url('logout') }}" class="active">@tr('logout')</a>
        @else
        <a href="{{ url('login') }}">@tr('login')</a>
        <a href="{{ url('register/student') }}" class="active">@tr('register')</a>
        @endif
    </div>

    @if(auth()->check())
    <div class="head-notifications">
        <i class="material-icons" style="cursor: pointer;" data-toggle="notifications">notifications_none</i>
        <span class="count">{{ auth()->user()->headNotifications()->count() }}</span>
        <div class="dropdown-content is-left no-padding" id="notifications">
            <div class="notifications">
                <a class="markAllAsRead" href="{{ url('/user/notification/mark_read') }}">@tr('mark_all_as_read')</a>
                @foreach(auth()->user()->headNotifications() as $notification)
                <div class="notification">
                    <span class="time">{{ $notification->created_at->diffForHumans() }}</span>
                    <a
                        href="{{ url('user/notification/'.$notification->id.'/read') }}">{{ $notification->notification }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(auth()->check())
    <div class="head-messages">
        <i class="material-icons" style="cursor: pointer;" data-toggle="messages">chat</i>
        <div class="dropdown-content is-left no-padding" id="messages">
            <div class="conversations">
                @foreach(auth()->user()->lastConversations() as $conversation)
                <div class="conversation">
                    <span class="time">{{ $conversation->created_at->diffForHumans() }}</span>
                    <a
                        href="{{ $conversation['conversation_url'] }}">{{ App\User::find($conversation['instructor_id'])->name }}</a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if(auth()->check())
    <div class="head-account has-dropdown">
        <div class="content">
            <i class="material-icons" style="cursor: pointer; margin-right: 10px;"
                data-toggle="account">account_circle</i>
            <span data-toggle="account">{{ auth()->user()->name }}</span>
        </div>
        <div class="dropdown-content no-padding" id="account">
            <div class="account">
                @if(auth()->user()->type==='admin')
                <a href="{{ url('admin') }}">@tr('dashboard')</a>
                @endif
                @if(auth()->user()->type==='student')
                <a href="{{ url('student/dashboard') }}">@tr('dashboard')</a>
                @endif
                @if(auth()->user()->hasCompany())
                <a href="{{ url('company/dashboard') }}">@tr('company_dashboard')</a>
                @endif
                @if(auth()->user()->type==='instructor')
                <a href="{{ url('instructor/dashboard') }}">@tr('dashboard')</a>
                @endif
                <a href="{{ url('account/wishlist') }}">@tr('wishlist')</a>
                <a href="{{ url('account/courses') }}">@tr('courses')</a>
                <a href="{{ url('account/payments') }}">@tr('payments')</a>
                <a href="{{ url('account/profile') }}">@tr('profile')</a>
                <a href="{{ url('logout') }}">@tr('logout')</a>
            </div>
        </div>
    </div>
    @endif

    <div class="transitioned has-dropdown" style="margin: 0 25px;">
        <span style="text-transform: uppercase; cursor: pointer;"
            data-toggle="language-switch">{{ app()->getLocale() }}</span>
        <div class="dropdown-content is-left no-padding" id="language-switch">
            <div class="language-switch">
                @foreach(App\Language::all() as $language)
                <a href="{{ url('locale/'.$language->slug) }}">{{ $language->title }}</a>
                @endforeach
            </div>
        </div>
    </div>

</nav>
<nav class="nav-mobile">
    <a href="#" onclick="showSidebar()">
        <i class="material-icons">menu</i>
    </a>
    <a href="{{ url('/') }}">
        <img src="{{ asset('public/design/assets/logo-blue.png') }}" />
    </a>
    <a href="#" onclick="showSearch()">
        <i class="material-icons">search</i>
    </a>
</nav>
