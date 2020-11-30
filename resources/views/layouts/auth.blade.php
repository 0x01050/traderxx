<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>
    @yield('meta')
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')  
        
    <script>
        var URL = {
            'base' : '{{ url('/') }}',
            'current' : '{{ url()->current() }}',
            'full' : '{{ url()->full() }}',
            'previous': '{{ url()->previous() }}'
        };
        var token = '{{ csrf_token() }}';
    </script>
</head>
<body>
    <div id="app">
        @yield('content')
    </div>
    @yield('scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
