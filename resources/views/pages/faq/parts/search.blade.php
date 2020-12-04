<div class="search">
    <h2 class="search__title">{{ __('faq.how_we_can_help') }}</h2>
    <form class="search__form">
        @csrf
        <input placeholder="{{ $search_param ?? __('faq.start_searching') }}" name="search_param" type="text" class="search__input" value="{{ $search_param ?? "" }}"/>
        <button type="submit" class="search__button">
            <img class="search__icon" src="{{ asset('img/icon/search-icon.svg') }}" alt="search icon">
            <span>{{ __('faq.search') }}</span>
        </button>
    </form>
    <p class="search__description">{{ __('faq.search_description') }}</p>
</div>
