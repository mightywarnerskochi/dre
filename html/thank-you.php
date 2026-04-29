<?php
$page_title = 'Thank You | Distinguished Real Estate';
$page_key = 'thank-you';
$body_class = 'thank-you-page';
$meta_description = 'Thank you — we have received your submission. Distinguished Real Estate will be in touch soon.';
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
    <section class="thank-you commonPadding-120" aria-labelledby="thank-you-title">
        <div class="container-ctn d-flex flex-column align-items-center">
            <div class="thank-you__illustration" role="img" aria-label="Message sent — envelope and paper plane">
                <img src="public/images/thank-you.png" alt="Thank You" width="414" height="414">
                
            </div>
            <h1 class="thank-you__title" id="thank-you-title">Thank You</h1>
            <p class="thank-you__text">Your submission has been received. We will be in touch and contact you soon.</p>
            <a class="btn btn-theme thank-you__cta" href="index.php">Back to Home Page</a>
        </div>
    </section>
<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';

require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';
