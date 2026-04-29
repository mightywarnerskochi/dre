<?php
// When Apache returns this as ErrorDocument, the browser URL can be a non-existent path; a
// <base> tag (set below) makes relative "index.php" / "public/..." links resolve from the
// app folder (e.g. /dre-web/) instead of the wrong directory.
$__base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
if ($__base === '/' || $__base === '.') {
    $__base = '';
}
$__scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443' ? 'https' : 'http';
$__host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$__path = ($__base !== '' ? rtrim($__base, '/') . '/' : '/');
$html_base_href = $__scheme . '://' . $__host . $__path;

$h = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};

if (!headers_sent()) {
    http_response_code(404);
}

$page_title = 'Page Not Found | Distinguished Real Estate';
$page_key = 'error-404';
$body_class = 'error-404-page';
$meta_description = 'The page you are looking for could not be found. Return to Distinguished Real Estate or contact support.';
$style_version = '63';
require __DIR__ . '/includes/header.php';
?>
    <section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img src="public/images/inner-banner.jpg" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
               
            </div>
        </div>
    </section>

    <section class="error-404 commonPadding-120" aria-labelledby="error-404-title">
        <div class="error-404__inner">
            <div class="error-404__gfx">
                <img src="public/images/404.png" alt="Page Not Found" width="100%" height="100%">
            </div>
            <h1 class="error-404__title" id="error-404-title">Page Not Found</h1>
            <p class="error-404__lead">
                Sorry, the page you are looking for does not exist. If you think something is broken, report a problem.
            </p>
            <div class="error-404__actions">
                <a class="btn btn-theme" href="index.php">Back to Home Page</a>
                <a class="btn btn-theme error-404__btn-outline" href="contact.php">Contact Support</a>
            </div>
        </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';
require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';
