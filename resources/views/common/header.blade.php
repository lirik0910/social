<header class="header">
    <div class="header__container">
        <img class="header__logo" src="{{ asset('img/icon/logo_color.svg') }}" alt="logo" />

        <div class="header__buttons">
            <div class="header__language_chooser">
                <span class="header__language_chooser__active_lang">{{  __('common.language_' . app()->getLocale()) }}</span>
                <div class="header__language_chooser__options_list">
                    @foreach($available_locales as $available_locale)
                        <div class="header__language_chooser__option"><a href="{{ url($available_locale['uri']) }}">{{ $available_locale['title'] }}</a></div>
                    @endforeach
                </div>
            </div>

            <button class="header__signup">Sign up</button>
        </div>
    </div>
</header>
