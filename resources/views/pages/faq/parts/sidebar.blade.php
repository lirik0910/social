@php
    $current_url = \Illuminate\Support\Facades\Request::url();

@endphp

<section class="navigation">
    <h2 class="navigation__title">{{ __('faq.help') }}</h2>

    <ul class="navigation__list">
        @foreach($categories as $category)
            <li class="navigation__item {{ $current_url === route('faq_category', ['url' => $category->slug]) ? "navigation__item-active" : ""}}">
                <a href="{{ route('faq_category', ['url' => $category->slug]) }}">
                    {{ $category->title }}
                </a>
            </li>
        @endforeach
    </ul>

    <ul class="navigation__list">
        <li class="navigation__item {{ $current_url === route('public_offer') ? "navigation__item-active" : ""}}"><a href="{{ route('public_offer') }}">{{ __('common.public_offer') }}</a></li>
        <li class="navigation__item {{ $current_url === route('terms_of_use') ? "navigation__item-active" : "" }}"><a href="{{ route('terms_of_use') }}">{{ __('common.terms_of_use') }}</a></li>
        <li class="navigation__item {{ $current_url === route('privacy_policy') ? "navigation__item-active" : "" }}"><a href="{{ route('privacy_policy') }}">{{ __('common.privacy_policy') }}</a></li>
        <li class="navigation__item {{ $current_url === route('contact_us') ? "navigation__item-active" : "" }}"><a href="{{ route('contact_us') }}">{{ __('common.contact_us') }}</a></li>
    </ul>
</section>

