<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}" type="image/x-icon">

    <!-- Open Graph / SEO -->
    <meta property="og:title" content="Boughanmi Patisserie" />
    <meta property="og:description" content="The finest pastry shop offering authentic and modern sweets." />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:image" content="{{ asset('favicon.png') }}" />
    <meta name="google-site-verification" content="WoIuJfJpXKTe332wCj8GAAKYtan4eAXeX8cuQUSZa6A" />
    <meta name="description"
        content="Boughanmi Patisserie - The finest pastry shop offering authentic and modern sweets.">


    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
