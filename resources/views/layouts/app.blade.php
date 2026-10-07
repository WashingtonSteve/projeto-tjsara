<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Task Management API')</title>
    <link rel="stylesheet" href="{{ asset('vendor/app.css') }}">
    <script src="{{ asset('vendor/alpine.min.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="h-full font-sans antialiased text-slate-900">
    @yield('content')
    @stack('scripts')
</body>
</html>
