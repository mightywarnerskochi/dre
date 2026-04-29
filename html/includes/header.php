<?php
$page_title = $page_title ?? 'Distinguished Real Estate';
$meta_description = $meta_description ?? 'List your property, earn more, and let us manage everything. Premium vacation home management in Dubai.';
$style_version = $style_version ?? '14';
$body_class = $body_class ?? '';
$page_key = $page_key ?? 'home';
$is_home = $page_key === 'home';
$idx = 'index.php';
$prop = 'properties.php';
$about_page = 'about.php';
$insights_page = 'insights.php';
$careers_page = 'career.php';
$contact_page = 'contact.php';
$book_viewing_page = 'book-a-viewing.php';
$map_page = 'map.php';
$is_about = $page_key === 'about';
$is_properties = $page_key === 'properties';
$is_insights = $page_key === 'insights';
$is_careers = $page_key === 'careers';
$is_contact = $page_key === 'contact';
$is_book_viewing = $page_key === 'book-viewing';
$body_attr = $body_class !== '' ? ' class="' . htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8') . '"' : '';
$h = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <?php if (!empty($html_base_href)) : ?>
    <base href="<?php echo $h($html_base_href); ?>">
    <?php endif; ?>
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $h($page_title); ?></title>
    <meta name="description" content="<?php echo $h($meta_description); ?>">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://code.jquery.com">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">

    <link rel="shortcut icon" href="public/images/fav.png" type="image/png" />
    <link rel="apple-touch-icon" sizes="128x128" href="public/images/fav.png" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="public/css/lib/slick-full.css" />
    <link rel="stylesheet" href="public/css/lib/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <link rel="stylesheet" href="public/scss/style.css?v=<?php echo $h($style_version); ?>">
    <?php if (strpos($body_class, 'property-details-page') !== false) : ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0.33/dist/fancybox/fancybox.css" crossorigin="anonymous" />
    <?php endif; ?>
</head>

<body<?php echo $body_attr; ?>>

    <header>
        <div class="container-ctn">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <a href="index.php" class="brand">
                    <picture>
                        <img src="public/images/logo.png" width="320" height="50" class="img-fluid" alt="Cariox">
                    </picture>
                </a>

                <div class="header-right d-flex align-items-center">
                    <!-- <a href="<?php echo $h($book_viewing_page); ?>" class="header-btn d-none d-md-inline-flex">Book a Viewing</a> -->
                    <div class="dropdown lang-switcher">
                        <button
                            class="lang-switcher__toggle"
                            type="button"
                            id="langSwitcherMenu"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                            aria-label="Language switcher"
                        >
                            <img src="public/images/flag.png" alt="English flag" class="lang-switcher__flag" data-lang-flag width="22" height="16">
                            <span class="lang-switcher__label" data-lang-label>ENG</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu lang-switcher__menu" aria-labelledby="langSwitcherMenu">
                            <li>
                                <button class="dropdown-item" type="button" data-lang-choice="en">
                                    <img src="public/images/english.jpg" alt="" width="22" height="16">
                                    <span>English</span>
                                </button>
                            </li>
                            <li>
                                <button class="dropdown-item" type="button" data-lang-choice="ar">
                                    <img src="public/images/arabic.jpg" alt="" width="22" height="16">
                                    <span>Arabic</span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <button class="navbar-toggler " type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#burgerMenu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <path
                                d="M20.0001 11.3766C20.4037 11.3766 20.7907 11.2163 21.0761 10.931C21.3615 10.6456 21.5218 10.2586 21.5218 9.85498C21.5218 9.45141 21.3615 9.06437 21.0761 8.779C20.7907 8.49363 20.4037 8.33331 20.0001 8.33331C19.5965 8.33331 19.2095 8.49363 18.9241 8.779C18.6388 9.06437 18.4785 9.45141 18.4785 9.85498C18.4785 10.2586 18.6388 10.6456 18.9241 10.931C19.2095 11.2163 19.5965 11.3766 20.0001 11.3766ZM30.1468 11.3766C30.3466 11.3766 30.5445 11.3373 30.7291 11.2608C30.9137 11.1843 31.0815 11.0723 31.2228 10.931C31.3641 10.7897 31.4762 10.6219 31.5526 10.4373C31.6291 10.2527 31.6685 10.0548 31.6685 9.85498C31.6685 9.65515 31.6291 9.45728 31.5526 9.27266C31.4762 9.08805 31.3641 8.9203 31.2228 8.779C31.0815 8.6377 30.9137 8.52561 30.7291 8.44914C30.5445 8.37267 30.3466 8.33331 30.1468 8.33331C29.7432 8.33331 29.3562 8.49363 29.0708 8.779C28.7854 9.06437 28.6251 9.45141 28.6251 9.85498C28.6251 10.2586 28.7854 10.6456 29.0708 10.931C29.3562 11.2163 29.7432 11.3766 30.1468 11.3766ZM9.85345 11.3766C10.0533 11.3766 10.2512 11.3373 10.4358 11.2608C10.6204 11.1843 10.7881 11.0723 10.9294 10.931C11.0707 10.7897 11.1828 10.6219 11.2593 10.4373C11.3358 10.2527 11.3751 10.0548 11.3751 9.85498C11.3751 9.65515 11.3358 9.45728 11.2593 9.27266C11.1828 9.08805 11.0707 8.9203 10.9294 8.779C10.7881 8.6377 10.6204 8.52561 10.4358 8.44914C10.2512 8.37267 10.0533 8.33331 9.85345 8.33331C9.44988 8.33331 9.06284 8.49363 8.77747 8.779C8.4921 9.06437 8.33179 9.45141 8.33179 9.85498C8.33179 10.2586 8.4921 10.6456 8.77747 10.931C9.06284 11.2163 9.44988 11.3766 9.85345 11.3766ZM20.0001 21.5216C20.4037 21.5216 20.7907 21.3613 21.0761 21.076C21.3615 20.7906 21.5218 20.4036 21.5218 20C21.5218 19.5964 21.3615 19.2094 21.0761 18.924C20.7907 18.6386 20.4037 18.4783 20.0001 18.4783C19.5965 18.4783 19.2095 18.6386 18.9241 18.924C18.6388 19.2094 18.4785 19.5964 18.4785 20C18.4785 20.4036 18.6388 20.7906 18.9241 21.076C19.2095 21.3613 19.5965 21.5216 20.0001 21.5216ZM30.1468 21.5216C30.5504 21.5216 30.9374 21.3613 31.2228 21.076C31.5081 20.7906 31.6685 20.4036 31.6685 20C31.6685 19.5964 31.5081 19.2094 31.2228 18.924C30.9374 18.6386 30.5504 18.4783 30.1468 18.4783C29.7432 18.4783 29.3562 18.6386 29.0708 18.924C28.7854 19.2094 28.6251 19.5964 28.6251 20C28.6251 20.4036 28.7854 20.7906 29.0708 21.076C29.3562 21.3613 29.7432 21.5216 30.1468 21.5216ZM9.85345 21.5216C10.257 21.5216 10.6441 21.3613 10.9294 21.076C11.2148 20.7906 11.3751 20.4036 11.3751 20C11.3751 19.5964 11.2148 19.2094 10.9294 18.924C10.6441 18.6386 10.257 18.4783 9.85345 18.4783C9.44988 18.4783 9.06284 18.6386 8.77747 18.924C8.4921 19.2094 8.33179 19.5964 8.33179 20C8.33179 20.4036 8.4921 20.7906 8.77747 21.076C9.06284 21.3613 9.44988 21.5216 9.85345 21.5216ZM20.0001 31.6666C20.4037 31.6666 20.7907 31.5063 21.0761 31.221C21.3615 30.9356 21.5218 30.5486 21.5218 30.145C21.5218 29.7414 21.3615 29.3544 21.0761 29.069C20.7907 28.7836 20.4037 28.6233 20.0001 28.6233C19.5965 28.6233 19.2095 28.7836 18.9241 29.069C18.6388 29.3544 18.4785 29.7414 18.4785 30.145C18.4785 30.5486 18.6388 30.9356 18.9241 31.221C19.2095 31.5063 19.5965 31.6666 20.0001 31.6666ZM30.1468 31.6666C30.5504 31.6666 30.9374 31.5063 31.2228 31.221C31.5081 30.9356 31.6685 30.5486 31.6685 30.145C31.6685 29.7414 31.5081 29.3544 31.2228 29.069C30.9374 28.7836 30.5504 28.6233 30.1468 28.6233C29.7432 28.6233 29.3562 28.7836 29.0708 29.069C28.7854 29.3544 28.6251 29.7414 28.6251 30.145C28.6251 30.5486 28.7854 30.9356 29.0708 31.221C29.3562 31.5063 29.7432 31.6666 30.1468 31.6666ZM9.85345 31.6666C10.257 31.6666 10.6441 31.5063 10.9294 31.221C11.2148 30.9356 11.3751 30.5486 11.3751 30.145C11.3751 29.7414 11.2148 29.3544 10.9294 29.069C10.6441 28.7836 10.257 28.6233 9.85345 28.6233C9.44988 28.6233 9.06284 28.7836 8.77747 29.069C8.4921 29.3544 8.33179 29.7414 8.33179 30.145C8.33179 30.5486 8.4921 30.9356 8.77747 31.221C9.06284 31.5063 9.44988 31.6666 9.85345 31.6666Z"
                                stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile menu (single markup; links follow $page_key) -->
    <div class="offcanvas offcanvas-end mobile_left_menu" tabindex="-1" id="burgerMenu"
        aria-labelledby="mobileOffcanvasLabel" aria-modal="true" role="dialog">
        <div class="offcanvas-header mobile-menu__header">
            <h2 class="visually-hidden" id="mobileOffcanvasLabel">Mobile Menu</h2>
            <a href="index.php" class="mobile-menu__brand" aria-label="DRE Home">
                <img src="public/images/logo-blue.png" alt="DRE" width="160" height="48" loading="lazy"
                    class="img-fluid mobile-menu__logo" />
            </a>
            <button type="button" class="mobile-menu__close" data-bs-dismiss="offcanvas" aria-label="Close menu">
                <span class="mobile-menu__close-label">Close</span>
                <span class="mobile-menu__close-x" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1 13L13 1M13 13L1 1" stroke="currentColor" stroke-width="1.75"
                            stroke-linecap="round" />
                    </svg>
                </span>
            </button>
        </div>

        <div class="offcanvas-body mobile-menu__body">
            <nav class="mobile-menu__nav" aria-label="Mobile primary navigation">
                <ul class="mobile-menu__list">
                    <li>
                        <a href="<?php echo $h($idx); ?>" class="mobile-menu__link<?php echo $is_home ? ' is-active' : ''; ?>"<?php echo $is_home ? ' aria-current="page"' : ''; ?>>Home</a>
                    </li>
                    <li><a href="<?php echo $h($about_page); ?>" class="mobile-menu__link<?php echo $is_about ? ' is-active' : ''; ?>"<?php echo $is_about ? ' aria-current="page"' : ''; ?>>About Us</a></li>
                    <li class="mobile-menu__item has-sub">
                        <button class="mobile-menu__link mobile-menu__link--toggle" type="button"
                            data-bs-toggle="collapse" data-bs-target="#mobileMenuProperties" aria-expanded="false"
                            aria-controls="mobileMenuProperties">
                            Our Properties
                            <svg class="mobile-menu__chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.2"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <ul class="collapse mobile-menu__sub" id="mobileMenuProperties">
                            <li><a href="<?php echo $h($prop); ?>"<?php echo $is_properties ? ' class="is-active" aria-current="page"' : ''; ?>>All listings</a></li>
                            <li><a href="<?php echo $h($map_page); ?>">Map view</a></li>
                        </ul>
                    </li>
                    <li><a href="<?php echo $h($insights_page); ?>" class="mobile-menu__link<?php echo $is_insights ? ' is-active' : ''; ?>"<?php echo $is_insights ? ' aria-current="page"' : ''; ?>>Insights</a></li>
                    <li><a href="<?php echo $h($careers_page); ?>" class="mobile-menu__link<?php echo $is_careers ? ' is-active' : ''; ?>"<?php echo $is_careers ? ' aria-current="page"' : ''; ?>>Careers</a></li>
                    <li><a href="<?php echo $h($contact_page); ?>" class="mobile-menu__link<?php echo $is_contact ? ' is-active' : ''; ?>"<?php echo $is_contact ? ' aria-current="page"' : ''; ?>>Contact</a></li>
                    <!-- <li><a href="<?php echo $h($book_viewing_page); ?>" class="mobile-menu__link mobile-menu__link--cta<?php echo $is_book_viewing ? ' is-active' : ''; ?>"<?php echo $is_book_viewing ? ' aria-current="page"' : ''; ?>>Book a Viewing</a></li> -->
                </ul>
            </nav>
            <div class="footer">
                <div class="mobile-menu__divider" role="presentation"></div>

                <div class="mobile-menu__contact">
                    <p class="mobile-menu__line">
                        <svg class="mobile-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"
                                fill="currentColor" />
                        </svg>
                        <span>
                            <a href="tel:+97143438302">+971 4 343 8302</a>
                            <span class="mobile-menu__sep">|</span>
                            <a href="tel:+97143438511">+971 4 343 8511</a>
                        </span>
                    </p>
                    <p class="mobile-menu__line">
                        <svg class="mobile-menu__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path
                                d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"
                                fill="currentColor" />
                        </svg>
                        <span>
                            <a href="mailto:info@dreuae.ae">info@dreuae.ae</a>
                            <span class="mobile-menu__sep">|</span>
                            <a href="mailto:info@dreuae.ae">info@dreuae.ae</a>
                        </span>
                    </p>
                </div>

                <ul class="mobile-menu__social" aria-label="Social links">
                    <li>
                        <a href="#" aria-label="Facebook" class="mobile-menu__social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="Twitter" class="mobile-menu__social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="LinkedIn" class="mobile-menu__social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="Instagram" class="mobile-menu__social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="#" aria-label="YouTube" class="mobile-menu__social-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="https://wa.me/971503697355" aria-label="WhatsApp" class="mobile-menu__social-link"
                            target="_blank" rel="noopener noreferrer">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="currentColor">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .004 5.411.001 12.045c0 2.12.554 4.189 1.602 6.06L0 24l6.124-1.605a11.815 11.815 0 005.923 1.577h.005c6.632 0 12.042-5.411 12.045-12.047a11.75 11.75 0 00-3.526-8.52z" />
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
