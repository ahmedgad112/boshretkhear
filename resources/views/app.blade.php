<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="بشرة خير — منصة عربية لعرض الشقق للبيع والإيجار. تصفّح وحدات سكنية بصور وتفاصيل كاملة وتواصل معنا للاستفسار.">
        <meta name="keywords" content="شقة, شقق, شقة للبيع, شقة للإيجار, بيع شقق, إيجار شقق, عقارات, وحدات سكنية, بشرة خير">
        <meta name="robots" content="index, follow">
        <meta name="author" content="بشرة خير">
        <meta name="language" content="Arabic">
        <meta property="og:locale" content="ar_EG">
        <meta property="og:site_name" content="بشرة خير">
        <meta name="twitter:card" content="summary_large_image">
        <title inertia>{{ config('app.name', 'بشرة خير') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-cairo antialiased bg-cream text-ink">
        @inertia
    </body>
</html>
