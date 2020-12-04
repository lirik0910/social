<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <title>{{ config('app.name', 'BuyDating') }}</title>
        <meta name="viewport" content="width=device-width">
        <meta name="theme-color" content="#000000"/>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="route" content="{{ Route::current()->getName() }}">

        <link rel="icon" href="{{ asset('img/icon/favicon.png') }}">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
{{--        <link rel="stylesheet" href="{{ asset('css/style.css') }}">--}}
        <link rel="stylesheet" href="{{ asset('css/app.min.css') }}">

    </head>

    <body>
        @include('common.header')

        <div class="loadIndicator"></div>

        @yield('content')

        @include('common.footer')

        <script src="{{ asset('js/app.min.js') }}"></script>
    </body>
</html>
