@php
    $styleVersion = $styleVersion ?? '14';
    $dreSitePublic = $sitePublic ?? ['phone1' => null, 'phone2' => null, 'email' => null, 'social' => []];
    $dreContentPublic = $contentPublic ?? [];
@endphp
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <script>
        (function () {
            try {
                var savedLang = localStorage.getItem("dre_lang") || "en";
                var root = document.documentElement;
                root.setAttribute("lang", savedLang);
                root.setAttribute("dir", savedLang === "ar" ? "rtl" : "ltr");
            } catch (e) {}
        })();
    </script>
    <script>
        window.__DRE_SITE__ = @json($dreSitePublic);
    </script>
    <script>
        window.__DRE_CONTENT__ = @json($dreContentPublic);
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Distinguished Real Estate') }}</title>
    <meta name="description" content="List your property, earn more, and let us manage everything. Premium vacation home management in Dubai.">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link rel="shortcut icon" href="/images/fav.png" type="image/png" />
    <link rel="apple-touch-icon" sizes="128x128" href="/images/fav.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="/css/lib/slick-full.css" />
    <link rel="stylesheet" href="/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <link rel="stylesheet" href="{{ url('/scss/style.css') }}?v={{ $styleVersion }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ url('/js/script.js') }}?v={{ $styleVersion }}" defer></script>
</body>
</html>
