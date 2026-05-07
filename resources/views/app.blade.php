@php
    $styleVersion = $styleVersion ?? '14';
    $dreSitePublic = $sitePublic ?? ['phone1' => null, 'phone2' => null, 'email' => null, 'social' => []];
    $dreContentPublic = $contentPublic ?? [];
    $dreTracking = is_array($dreSitePublic['tracking'] ?? null) ? $dreSitePublic['tracking'] : [];
    $dreGtmContainerIds = is_array($dreTracking['gtmContainerIds'] ?? null) ? $dreTracking['gtmContainerIds'] : [];
    $dreCustomHeadScript = is_string($dreTracking['customHeadScript'] ?? null) ? trim($dreTracking['customHeadScript']) : '';
    $dreCustomBodyScript = is_string($dreTracking['customBodyScript'] ?? null) ? trim($dreTracking['customBodyScript']) : '';

    $dreSeoPages = data_get($dreContentPublic, 'seo.pages', []);
    $dreSeoPages = is_array($dreSeoPages) ? $dreSeoPages : [];
    $dreDefaultSeoText = 'Distinguished Real Estate';

    $dreNormalizeSeoKey = static function (mixed $value): string {
        return preg_replace('/[^a-z0-9]+/i', '', strtolower(trim((string) $value))) ?? '';
    };

    $dreReadSeoValue = static function (array $entry, string $field): string {
        $raw = data_get($entry, $field);
        if (is_string($raw)) {
            return trim($raw);
        }
        if (! is_array($raw)) {
            return '';
        }

        $english = trim((string) ($raw['en'] ?? ''));
        if ($english !== '') {
            return $english;
        }

        foreach ($raw as $value) {
            $text = trim((string) $value);
            if ($text !== '') {
                return $text;
            }
        }

        return '';
    };

    $dreResolveSeoEntry = static function (string $requestedKey, array $pages) use ($dreNormalizeSeoKey): array {
        $requestedKey = trim($requestedKey);
        if ($requestedKey !== '' && isset($pages[$requestedKey]) && is_array($pages[$requestedKey])) {
            return $pages[$requestedKey];
        }

        $aliasesByKey = [
            'home' => ['home', 'home-page', 'homepage', 'index'],
            'about' => ['about', 'about-us', 'aboutus'],
            'properties' => ['properties', 'property', 'our-property', 'ourproperty'],
            'blogs' => ['blogs', 'blog', 'insights', 'news', 'news-insights'],
            'careers' => ['careers', 'career', 'jobs'],
            'contact' => ['contact', 'contact-us', 'contactus'],
            'map' => ['map', 'map-search', 'mapsearch'],
            'terms' => ['terms', 'terms-conditions', 'termsandconditions'],
            'privacy' => ['privacy', 'privacy-policy', 'privacypolicy'],
            'disclaimer' => ['disclaimer'],
            'cookie' => ['cookie', 'cookie-policy', 'cookiepolicy'],
        ];

        $candidates = $aliasesByKey[$requestedKey] ?? [$requestedKey];
        $candidateNormalized = [];
        foreach ($candidates as $candidate) {
            $normalized = $dreNormalizeSeoKey($candidate);
            if ($normalized !== '') {
                $candidateNormalized[$normalized] = true;
            }
        }

        foreach ($pages as $key => $payload) {
            if (! is_array($payload)) {
                continue;
            }
            $normalizedKey = $dreNormalizeSeoKey($key);
            if ($normalizedKey !== '' && isset($candidateNormalized[$normalizedKey])) {
                return $payload;
            }
        }

        return [];
    };

    $drePath = trim((string) request()->path(), '/');
    $dreFirstSegment = $drePath === '' ? 'home' : explode('/', $drePath)[0];
    $dreRouteToSeoKey = [
        'home' => 'home',
        'about' => 'about',
        'our-property' => 'properties',
        'properties' => 'properties',
        'property-details' => 'properties',
        'insights' => 'blogs',
        'insights-details' => 'blogs',
        'career' => 'careers',
        'career-details' => 'careers',
        'contact' => 'contact',
        'map' => 'map',
        'terms-conditions' => 'terms',
        'privacy-policy' => 'privacy',
        'disclaimer' => 'disclaimer',
        'cookie-policy' => 'cookie',
    ];
    $dreRequestedSeoKey = $dreRouteToSeoKey[$dreFirstSegment] ?? $dreFirstSegment;
    $dreInitialSeoEntry = $dreResolveSeoEntry($dreRequestedSeoKey, $dreSeoPages);

    $dreInitialMetaTitle = $dreReadSeoValue($dreInitialSeoEntry, 'metaTitle');
    if ($dreInitialMetaTitle === '') {
        $dreInitialMetaTitle = $dreDefaultSeoText;
    }

    $dreInitialMetaDescription = $dreReadSeoValue($dreInitialSeoEntry, 'metaDescription');
    if ($dreInitialMetaDescription === '') {
        $dreInitialMetaDescription = $dreDefaultSeoText;
    }

    $dreInitialMetaKeywords = $dreReadSeoValue($dreInitialSeoEntry, 'metaKeywords');
    $dreInitialCanonicalUrl = $dreReadSeoValue($dreInitialSeoEntry, 'canonicalUrl');
    if ($dreInitialCanonicalUrl === '') {
        $dreInitialCanonicalUrl = url()->current();
    } elseif (! preg_match('/^https?:\/\//i', $dreInitialCanonicalUrl)) {
        $dreInitialCanonicalUrl = url($dreInitialCanonicalUrl);
    }

    $dreInitialOgTitle = $dreReadSeoValue($dreInitialSeoEntry, 'ogTitle');
    if ($dreInitialOgTitle === '') {
        $dreInitialOgTitle = $dreInitialMetaTitle;
    }

    $dreInitialOgDescription = $dreReadSeoValue($dreInitialSeoEntry, 'ogDescription');
    if ($dreInitialOgDescription === '') {
        $dreInitialOgDescription = $dreInitialMetaDescription;
    }

    $dreInitialOgImage = trim((string) data_get($dreInitialSeoEntry, 'ogImage', ''));
    if ($dreInitialOgImage === '') {
        $dreInitialOgImage = trim((string) (data_get($dreSitePublic, 'colourLogoUrl') ?: data_get($dreSitePublic, 'logoUrl') ?: ''));
    }
    if ($dreInitialOgImage !== '' && ! preg_match('/^https?:\/\//i', $dreInitialOgImage)) {
        $dreInitialOgImage = url($dreInitialOgImage);
    }
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
        window.__DRE_BOOT_ENDPOINT__ = @json(url('/api/public/bootstrap'));
    </script>
    <script>
        window.__DRE_RECAPTCHA_SITE_KEY__ = @json(config('services.recaptcha.site_key'));
        window.__DRE_RECAPTCHA_ENABLED__ = @json((bool) config('services.recaptcha.enabled'));
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $dreInitialMetaTitle }}</title>
    <meta name="description" content="{{ $dreInitialMetaDescription }}">
    <meta name="keywords" content="{{ $dreInitialMetaKeywords }}">
    <meta property="og:title" content="{{ $dreInitialOgTitle }}">
    <meta property="og:description" content="{{ $dreInitialOgDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $dreInitialCanonicalUrl }}">
    <meta property="og:image" content="{{ $dreInitialOgImage }}">
    <link rel="canonical" id="dre-canonical-link" href="{{ $dreInitialCanonicalUrl }}">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link rel="shortcut icon" href="{{ asset('images/fav.png') }}" type="image/png" />
    <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('images/fav.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="{{ asset('css/lib/slick-full.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lib/jquery.fancybox.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/lib/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <link rel="stylesheet" href="{{ asset('scss/style.css') }}?v={{ $styleVersion }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.css" />
    <style>
        .grecaptcha-badge {
            visibility: hidden !important;
        }
    </style>

    @foreach($dreGtmContainerIds as $gtmContainerId)
        <script>
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','{{ $gtmContainerId }}');
        </script>
    @endforeach

    @if($dreCustomHeadScript !== '')
        {!! $dreCustomHeadScript !!}
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @foreach($dreGtmContainerIds as $gtmContainerId)
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmContainerId }}"
                    height="0"
                    width="0"
                    style="display:none;visibility:hidden"></iframe>
        </noscript>
    @endforeach

    @if($dreCustomBodyScript !== '')
        {!! $dreCustomBodyScript !!}
    @endif

    <div id="app"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    @if(config('services.recaptcha.enabled') && filled(config('services.recaptcha.site_key')))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.0/slick.min.js"></script>
    <script src="{{ asset('js/lib/jquery.fancybox.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/script.js') }}?v={{ $styleVersion }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.7.1/nouislider.min.js"></script>
</body>
</html>
