<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Laravel') }}</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Hetrick Painting, Inc.">
        @yield('meta')

        <!-- stylesheets -->
        <link href="{{ url('/css/app.css') }}" rel=stylesheet >
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
            <page-header v-bind:name="'{{ Auth::user()->name }}'"></page-header>
            @yield('content')
        </div>
        @yield('scripts')
        <script src="{{ url('/js/app.js') }}"></script>
    </body>
</html>