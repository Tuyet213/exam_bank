<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <!-- chuyển các route từ laravel sang javascript để có thể sử dụng trong Vue -->
        @routes 

        <!-- jj
        resources/js/app.js: Tệp khởi động chính của Vue.
        resources/js/Pages/{$page['component']}.vue: Đây là cơ chế Dynamic Import, giúp Laravel + Inertia.js chỉ tải trang Vue tương ứng khi cần. -->
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])

        <!-- Cho phép Inertia.js thao tác với thẻ <head> của trang. -->
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        <!-- nơi mà Inertia.js render các component Vue -->
        @inertia
    </body>
</html>
