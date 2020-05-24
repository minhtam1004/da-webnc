<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">

        <!-- Main styles for this application -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <meta id="csrf-token" name="csrf-token" value="{{ csrf_token() }}">
    </head>
    <body>
        <div id="app">
            <v-app>
            <router-view></router-view>
            </v-app>
        </div>
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!-- <script src="{{ mix('js/app.js') }}"></script> -->
    </body>
</html>