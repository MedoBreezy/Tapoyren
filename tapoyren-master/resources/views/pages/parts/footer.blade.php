<div class="footer">

    <div class="col col-about">
        <img src="{{ asset('public/design/assets/logo-white.png') }}" class="logo" />
        <p class="is-desktop">TapÖyrən @tr('tapoyren_is')</p>
    </div>

    <div class="col col-links">
        <h3>@tr('learn')</h3>
        <ul>
            @foreach(App\Category::where('parent_id',null)->take(5)->get() as $footerCat)
            <li>
                <a href="{{ url('category/'.$footerCat->id) }}"
                    class="transitioned hover-opacity">{{ $footerCat->__('title') }}</a>
            </li>
            @endforeach
        </ul>
    </div>

    <div class="col col-links">
        <h3>@tr('links')</h3>
        <ul>
            <li>
                <a href="{{ url('about') }}" class="transitioned hover-opacity">@tr('about')</a>
            </li>
            <li>
                <a href="{{ url('contact') }}" class="transitioned hover-opacity">@tr('contact')</a>
            </li>
            <li>
                <a href="{{ url('faq') }}" class="transitioned hover-opacity">@tr('faq')</a>
            </li>
            <li>
                <a href="{{ url('terms_and_conditions') }}"
                    class="transitioned hover-opacity">@tr('terms_and_conditions')</a>
            </li>
        </ul>
    </div>

    <div class="col col-contact">
        <h3>@tr('about')</h3>
        <div class="contact_details">
            <div>
                <i class="material-icons">location_on</i>
                <span>@tr('location')</span>
            </div>
            <div>
                <i class="material-icons">phone</i>
                <span>+99455 667 00 57</span>
            </div>
            <div>
                <i class="material-icons">mail_outline</i>
                <span>SUPPORT@TAPOYREN.COM</span>
            </div>
        </div>
    </div>

</div>

<div class="copyright">
    <div class="left">
        © {{ date('Y') }}. @tr('all_rights_reserved')
    </div>
</div>
