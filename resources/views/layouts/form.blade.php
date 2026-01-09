<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <title>{{ $active ?? '' ? "$active |" : '' }}Berbagi Bites Jogja</title>
    <meta name="description"
        content="Berbagi Bites Jogja (BBJ) adalah Gerakan food rescue pertama di Jogja. Bergerak mengatasi Food Waste melalui Food Rescue dan Food Bank di wilayah Yogyakarta">
    <meta property="og:locale" content="id_ID" />
    <meta property="og:logo" content="https://berbagibitesjogja.com/plain-logo-blue.png" />
    <meta property="og:image" content="https://berbagibitesjogja.com/shareable-card-preview.jpg">
    <meta property="og:url" content="https://berbagibitesjogja.com/">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Berbagi Bites Jogja">
    <meta property="og:description"
        content="Berbagi Bites Jogja (BBJ) adalah Gerakan food rescue pertama di Jogja. Bergerak mengatasi Food Waste melalui Food Rescue dan Food Bank di wilayah Yogyakarta">
    <meta property="og:image" content="https://berbagibitesjogja.com/shareable-card-preview.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="twitter:domain" content="berbagibitesjogja.com">
    <meta property="twitter:url" content="https://berbagibitesjogja.com/">
    <meta name="twitter:title" content="Berbagi Bites Jogja">
    <meta name="twitter:description"
        content="Berbagi Bites Jogja (BBJ) adalah Gerakan food rescue pertama di Jogja. Bergerak mengatasi Food Waste melalui Food Rescue dan Food Bank di wilayah Yogyakarta">
    <meta name="twitter:image" content="https://berbagibitesjogja.com/shareable-card-preview.jpg">

</head>

<body>
    @include('components.header')
    <main class="md:px-44 px-6 py-10">
        @yield('container')

    </main>
    @include('components.footer')
    @session('success')
        @include('components.toast_success')
    @endsession
    @session('error')
        @include('components.toast_error')
    @endsession
</body>

</html>
