<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ 'About Us | ' . ($pageData['site']['fullName'] ?? config('app.name')) }}</title>
    @if(!empty($pageData['site']['faviconUrl']))
    <link rel="icon" type="image/png" href="{{ $pageData['site']['faviconUrl'] }}">
    @endif
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/dre-variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dre-home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dre-page-shell.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dre-about.css') }}">
    @vite(['resources/js/about.js'])
</head>
<body>
    <script id="dre-about-page-data" type="application/json">@json($pageData)</script>
    <div id="dre-about-app"></div>
</body>
</html>
