@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">
    <div class="hero_auth">
        <div class="text">
            <img src="{{ asset('public/design/assets/tapoyren_icon.png') }}" />
            <div class="heading">{{ config('app.name') }}</div>
            <div class="description">@tr('tapoyren_is')</div>
        </div>

        <div class="auth">

            <div class="tabs">
                <div class="tab{{ request()->is('login') ? ' active' : '' }}" onclick="showTab('login',this)">
                    @tr('login')
                </div>
                <div class="tab{{ request()->is('register/student') ? ' active' : '' }}"
                    onclick="showTab('register',this)">
                    @tr('register')</div>
            </div>

            <form action="{{ url('login') }}" method="POST"
                class="tab-login content{{ request()->is('login') ? ' active' : '' }}">
                @csrf

                @if($errors->any())
                <div style="text-align: center; margin-bottom: 30px; padding: 0 10px;">{{ $errors->first() }}</div>
                @endif

                <div class="input">
                    <input type="email" name="email" />
                    <span class="placeholder">@tr('email')</span>
                </div>
                <div class="input">
                    <input type="password" name="password" />
                    <span class="placeholder">@tr('password')</span>
                </div>

                <div class="btm">

                    <div class="checkbox">
                        <input id="remember_me" type="checkbox" />
                        <div class="check" onclick="rememberMe()">
                            <i class="material-icons">done</i>
                        </div>
                        <label for="remember_me">@tr('remember_me')</label>
                    </div>

                    <a href="{{ url('forgot_password') }}" class="forgot">@tr('forgot_password')?</a>

                </div>

                <button class="blue">@tr('login')</button>

            </form>
            <form action="{{ url('register/student') }}" method="POST"
                class="tab-register content{{ request()->is('register/student') ? ' active' : '' }}">
                @csrf

                @if($errors->any())
                <div style="text-align: center; margin-bottom: 30px; padding: 0 10px;">{{ $errors->first() }}</div>
                @endif

                <div class="input">
                    <input type="text" name="name" />
                    <span class="placeholder">@tr('fullname')</span>
                </div>
                <div class="input">
                    <input type="email" name="email" />
                    <span class="placeholder">@tr('email')</span>
                </div>
                <select name="gender">
                    <option value="-">@tr('gender')</option>
                    <option value="male">@tr('male')</option>
                    <option value="female">@tr('female')</option>
                </select>
                <select name="birthDate" style="margin-top: 10px">
                    <option value="">@tr('birth_date')</option>
                    @for($i=1960; $i<2021; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
                <div class="input">
                    <input type="password" name="password" />
                    <span class="placeholder">@tr('password')</span>
                </div>
                <div class="input">
                    <input type="password" name="password_confirmation" />
                    <span class="placeholder">@tr('password_confirmation')</span>
                </div>

                <div class="checkbox">
                    <input id="agreeToTerms" name="agreeToTerms" type="checkbox" />
                    <div class="check" onclick="agreeToTerms()">
                        <i class="material-icons">done</i>
                    </div>
                    <label for="agreeToTerms">@tr('agree_to_terms')</label>
                    <a href="{{ url('terms_and_conditions') }}" target="_blank">
                        <i class="material-icons">visibility</i>
                    </a>
                </div>

                <button class="blue">@tr('register')</button>
            </form>

        </div>

    </div>


    @include('pages.parts.footer')
</main>



@endsection


@push('footer')

<script>
    var tabs = document.querySelectorAll('.hero_auth .auth .tabs .tab');
    var tab_contents = document.querySelectorAll('.hero_auth .auth .content');

    function showTab(tabName, el) {
        tabs.forEach(tab => tab.classList.remove('active'));
        tab_contents.forEach(tabContent => tabContent.classList.remove('active'));
        el.classList.add('active');
        document.querySelector('.tab-' + tabName).classList.add('active');
    }

    function rememberMe() {
        document.querySelector('.tab-login input[id=remember_me]').click();
    }

    function agreeToTerms() {
        document.querySelector('.tab-register input[id=agreeToTerms]').click();
    }

</script>

@endpush
