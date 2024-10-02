<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ToDo App</title>
    </head>
    <body class="antialiased">
        <div id="app">
            <div>
                <router-view></router-view>
                <ToasContainer />
            </div>
        </div>
        @vite('resources/js/app.js')
    </body>
</html>
