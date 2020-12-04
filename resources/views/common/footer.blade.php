<footer class="footer">
    <a href="#"><img class="footer__logo" src="{{ asset('img/icon/logo-grey.svg') }}" alt="logo"></a>

    <nav class="footer__links">
        <ul>
            <li class="footer__link"><a href="{{ route('public_offer') }}">{{ __('common.public_offer') }}</a></li>
            <li class="footer__link"><a href="{{ route('terms_of_use') }}">{{ __('common.terms_of_use') }}</a></li>
            <li class="footer__link"><a href="{{ route('privacy_policy') }}">{{ __('common.privacy_policy') }}</a></li>
            <li class="footer__link"><a href="{{ route('contact_us') }}">{{ __('common.contact_us') }}</a></li>
        </ul>
    </nav>

    <div class="footer__info">
        <div class="footer__lang">
            <span>Language:</span>
            <div class="footer__language_chooser">
                <span class="footer__language_chooser__active_lang">{{ __('common.language_' . app()->getLocale())}}</span>
                <div class="footer__language_chooser__options_list">
                    @foreach($available_locales as $available_locale)
                        <div class="footer__language_chooser__option"><a href="{{ url($available_locale['uri']) }}">{{ $available_locale['title'] }}</a></div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="footer__year">
            © 2020 BuyDating
        </div>
    </div>

    <div class="footer__services">
        <a href="#"><img class="footer__apple_icon" src="{{ asset('img/icon/apple.svg') }}" alt="apple"></a>
        <a href="#"><img class="footer__google_icon" src="{{ asset('img/icon/google-play.svg') }}" alt="google-play"></a>
    </div>
</footer>
