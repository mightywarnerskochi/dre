<?php
$page_title = 'Contact Us | Distinguished Real Estate';
$page_key = 'contact';
$body_class = 'contact-page';
$meta_description = 'Contact Distinguished Real Estate — get in touch for property enquiries and support in Dubai.';
$style_version = '54';
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
                <h1 class="banner--page__title">Contact Us</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <a href="index.php" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">Contact Us</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="contact-form commonPadding-120" aria-labelledby="contact-form-heading">
        <div class="container-ctn">
            <div class="d-flex flex-wrap justify-content-between">
                <div class=" contact-form__left">
                    <h2 class="contact-form__heading" id="contact-form-heading">Get in Touch</h2>
                    <div class="form-wrapper">
                        <h3 id="information-request-heading">Information Request</h3>
                        <p id="information-request-desc">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry&apos;s standard dummy text</p>
                        <form class="information-request-form" action="" method="post" aria-labelledby="information-request-heading" aria-describedby="information-request-desc">
                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-name">Name</label>
                                    <input class="form-control" type="text" name="name" id="contact-name" placeholder="Name" autocomplete="name" required>
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-email">Email</label>
                                    <input class="form-control" type="email" name="email" id="contact-email" placeholder="Email" autocomplete="email" required>
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-phone">Phone</label>
                                    <input class="form-control phone_number" type="tel"  name="phone" id="contact-phone" placeholder="Phone" autocomplete="tel">
                                </div>
                                <div class="col--6">
                                    <label class="visually-hidden" for="contact-subject">Subject</label>
                                    <input class="form-control" type="text" name="subject" id="contact-subject" placeholder="Subject">
                                </div>
                                <div class="col--12">
                                    <label class="visually-hidden" for="contact-message">Message</label>
                                    <textarea class="form-control" name="message" id="contact-message" rows="5" placeholder="Message" required></textarea>
                                </div>
                                <div class="col--12 mb-0">
                                    <button type="submit" class="btn btn-theme ms-auto">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="contact-form__right">
                    <h2 class="contact-offices__title">Office Locations</h2>
                    <div class="contact-offices">
                        <article class="contact-office" aria-label="Dubai office">
                            <div class="contact-office__map">
                                <img src="public/images/map-2.jpg" alt="Map: Distinguished Real Estate, Al Wasl Building, Dubai" width="600" height="220" loading="lazy">
                            </div>
                            <ul class="contact-office__list">
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M12 2.00012C7.6 2.00012 4 5.60012 4 10.0001C4 15.4001 11 21.5001 11.3 21.8001C11.5 21.9001 11.8 22.0001 12 22.0001C12.2 22.0001 12.5 21.9001 12.7 21.8001C13 21.5001 20 15.4001 20 10.0001C20 5.60012 16.4 2.00012 12 2.00012ZM12 19.7001C9.9 17.7001 6 13.4001 6 10.0001C6 6.70012 8.7 4.00012 12 4.00012C15.3 4.00012 18 6.70012 18 10.0001C18 13.3001 14.1 17.7001 12 19.7001ZM12 6.00012C9.8 6.00012 8 7.80012 8 10.0001C8 12.2001 9.8 14.0001 12 14.0001C14.2 14.0001 16 12.2001 16 10.0001C16 7.80012 14.2 6.00012 12 6.00012ZM12 12.0001C10.9 12.0001 10 11.1001 10 10.0001C10 8.90012 10.9 8.00012 12 8.00012C13.1 8.00012 14 8.90012 14 10.0001C14 11.1001 13.1 12.0001 12 12.0001Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text">Distinguished Real Estate Al Wasl Building - Office Number: M18 - Dubai</span>
                                </li>
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.73303 2.04305C6.95003 0.833052 8.95403 1.04805 9.97303 2.41005L11.235 4.09405C12.065 5.20205 11.991 6.75005 11.006 7.72905L10.768 7.96705C10.741 8.06696 10.7383 8.17187 10.76 8.27305C10.823 8.68105 11.164 9.54505 12.592 10.9651C14.02 12.3851 14.89 12.7251 15.304 12.7891C15.4083 12.81 15.5161 12.807 15.619 12.7801L16.027 12.3741C16.903 11.5041 18.247 11.3411 19.331 11.9301L21.241 12.9701C22.878 13.8581 23.291 16.0821 21.951 17.4151L20.53 18.8271C20.082 19.2721 19.48 19.6431 18.746 19.7121C16.936 19.8811 12.719 19.6651 8.28603 15.2581C4.14903 11.1441 3.35503 7.55605 3.25403 5.78805C3.20403 4.89405 3.62603 4.13805 4.16403 3.60405L5.73303 2.04305ZM8.77303 3.30905C8.26603 2.63205 7.32203 2.57805 6.79003 3.10705L5.22003 4.66705C4.89003 4.99505 4.73203 5.35705 4.75203 5.70305C4.83203 7.10805 5.47203 10.3451 9.34403 14.1951C13.406 18.2331 17.157 18.3541 18.607 18.2181C18.903 18.1911 19.197 18.0371 19.472 17.7641L20.892 16.3511C21.47 15.7771 21.343 14.7311 20.525 14.2871L18.615 13.2481C18.087 12.9621 17.469 13.0561 17.085 13.4381L16.63 13.8911L16.1 13.3591C16.63 13.8911 16.629 13.8921 16.628 13.8921L16.627 13.8941L16.624 13.8971L16.617 13.9031L16.602 13.9171C16.5598 13.9562 16.5143 13.9917 16.466 14.0231C16.386 14.0761 16.28 14.1351 16.147 14.1841C15.877 14.2851 15.519 14.3391 15.077 14.2711C14.21 14.1381 13.061 13.5471 11.534 12.0291C10.008 10.5111 9.41203 9.36905 9.27803 8.50305C9.20903 8.06105 9.26403 7.70305 9.36603 7.43305C9.42216 7.28112 9.50254 7.13928 9.60403 7.01305L9.63603 6.97805L9.65003 6.96305L9.65603 6.95705L9.65903 6.95405L9.66103 6.95205L9.94903 6.66605C10.377 6.23905 10.437 5.53205 10.034 4.99305L8.77303 3.30905Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text"><a href="tel:+97143438302">+971 4 343 8302</a> <span class="contact-office__sep" aria-hidden="true">|</span> <a href="tel:+97143438511">+971 4 343 8511</a></span>
                                </li>
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M22 5.99988C22 4.89988 21.1 3.99988 20 3.99988H4C2.9 3.99988 2 4.89988 2 5.99988V17.9999C2 19.0999 2.9 19.9999 4 19.9999H20C21.1 19.9999 22 19.0999 22 17.9999V5.99988ZM20 5.99988L12 10.9899L4 5.99988H20ZM20 17.9999H4V7.99988L12 12.9999L20 7.99988V17.9999Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text"><a href="mailto:info@dreuae.ae">info@dreuae.ae</a> <span class="contact-office__sep" aria-hidden="true">|</span> <a href="mailto:info@dreuae.ae">info@dreuae.ae</a></span>
                                </li>
                            </ul>
                        </article>
                        <article class="contact-office" aria-label="Dubai office">
                            <div class="contact-office__map">
                                <img src="public/images/map-2.jpg" alt="Map: Distinguished Real Estate, Al Wasl Building, Dubai" width="600" height="220" loading="lazy">
                            </div>
                            <ul class="contact-office__list">
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M12 2.00012C7.6 2.00012 4 5.60012 4 10.0001C4 15.4001 11 21.5001 11.3 21.8001C11.5 21.9001 11.8 22.0001 12 22.0001C12.2 22.0001 12.5 21.9001 12.7 21.8001C13 21.5001 20 15.4001 20 10.0001C20 5.60012 16.4 2.00012 12 2.00012ZM12 19.7001C9.9 17.7001 6 13.4001 6 10.0001C6 6.70012 8.7 4.00012 12 4.00012C15.3 4.00012 18 6.70012 18 10.0001C18 13.3001 14.1 17.7001 12 19.7001ZM12 6.00012C9.8 6.00012 8 7.80012 8 10.0001C8 12.2001 9.8 14.0001 12 14.0001C14.2 14.0001 16 12.2001 16 10.0001C16 7.80012 14.2 6.00012 12 6.00012ZM12 12.0001C10.9 12.0001 10 11.1001 10 10.0001C10 8.90012 10.9 8.00012 12 8.00012C13.1 8.00012 14 8.90012 14 10.0001C14 11.1001 13.1 12.0001 12 12.0001Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text">Distinguished Real Estate Al Wasl Building - Office Number: M18 - Dubai</span>
                                </li>
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5.73303 2.04305C6.95003 0.833052 8.95403 1.04805 9.97303 2.41005L11.235 4.09405C12.065 5.20205 11.991 6.75005 11.006 7.72905L10.768 7.96705C10.741 8.06696 10.7383 8.17187 10.76 8.27305C10.823 8.68105 11.164 9.54505 12.592 10.9651C14.02 12.3851 14.89 12.7251 15.304 12.7891C15.4083 12.81 15.5161 12.807 15.619 12.7801L16.027 12.3741C16.903 11.5041 18.247 11.3411 19.331 11.9301L21.241 12.9701C22.878 13.8581 23.291 16.0821 21.951 17.4151L20.53 18.8271C20.082 19.2721 19.48 19.6431 18.746 19.7121C16.936 19.8811 12.719 19.6651 8.28603 15.2581C4.14903 11.1441 3.35503 7.55605 3.25403 5.78805C3.20403 4.89405 3.62603 4.13805 4.16403 3.60405L5.73303 2.04305ZM8.77303 3.30905C8.26603 2.63205 7.32203 2.57805 6.79003 3.10705L5.22003 4.66705C4.89003 4.99505 4.73203 5.35705 4.75203 5.70305C4.83203 7.10805 5.47203 10.3451 9.34403 14.1951C13.406 18.2331 17.157 18.3541 18.607 18.2181C18.903 18.1911 19.197 18.0371 19.472 17.7641L20.892 16.3511C21.47 15.7771 21.343 14.7311 20.525 14.2871L18.615 13.2481C18.087 12.9621 17.469 13.0561 17.085 13.4381L16.63 13.8911L16.1 13.3591C16.63 13.8911 16.629 13.8921 16.628 13.8921L16.627 13.8941L16.624 13.8971L16.617 13.9031L16.602 13.9171C16.5598 13.9562 16.5143 13.9917 16.466 14.0231C16.386 14.0761 16.28 14.1351 16.147 14.1841C15.877 14.2851 15.519 14.3391 15.077 14.2711C14.21 14.1381 13.061 13.5471 11.534 12.0291C10.008 10.5111 9.41203 9.36905 9.27803 8.50305C9.20903 8.06105 9.26403 7.70305 9.36603 7.43305C9.42216 7.28112 9.50254 7.13928 9.60403 7.01305L9.63603 6.97805L9.65003 6.96305L9.65603 6.95705L9.65903 6.95405L9.66103 6.95205L9.94903 6.66605C10.377 6.23905 10.437 5.53205 10.034 4.99305L8.77303 3.30905Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text"><a href="tel:+97143438302">+971 4 343 8302</a> <span class="contact-office__sep" aria-hidden="true">|</span> <a href="tel:+97143438511">+971 4 343 8511</a></span>
                                </li>
                                <li class="contact-office__row">
                                    <span class="contact-office__icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <path d="M22 5.99988C22 4.89988 21.1 3.99988 20 3.99988H4C2.9 3.99988 2 4.89988 2 5.99988V17.9999C2 19.0999 2.9 19.9999 4 19.9999H20C21.1 19.9999 22 19.0999 22 17.9999V5.99988ZM20 5.99988L12 10.9899L4 5.99988H20ZM20 17.9999H4V7.99988L12 12.9999L20 7.99988V17.9999Z" fill="#2F3F58"/> </svg>
                                    </span>
                                    <span class="contact-office__text"><a href="mailto:info@dreuae.ae">info@dreuae.ae</a> <span class="contact-office__sep" aria-hidden="true">|</span> <a href="mailto:info@dreuae.ae">info@dreuae.ae</a></span>
                                </li>
                            </ul>
                        </article>
                 
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';

require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';
