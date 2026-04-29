<?php
$page_title = 'Career | Distinguished Real Estate';
$page_key = 'careers';
$body_class = 'careers-page';
$meta_description = 'careers Distinguished Real Estate — get in touch for property enquiries and support in Dubai.';
$style_version = '60';
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
                <h1 class="banner--page__title">Careers</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li>
                        <a href="index.php" aria-label="Home">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.5L12 3L21 10.5V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V10.5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">Careers </li>
                </ol>
            </div>
        </div>
    </section>

    <section class="career-openings commonPadding-120" aria-labelledby="career-openings-heading">
        <div class="container-ctn">
            <div class="head text-center">
                <h2 id="career-openings-heading">Current Opening</h2>
                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
            </div>

            <form class="career-openings__filters" action="" method="get" aria-label="Job filters">
                <div class="career-openings__filter">
                    <label class="sr-only" for="career-job-title">Job Title</label>
                    <select id="career-job-title" name="job_title">
                        <option value="">Job Title</option>
                        <option value="product-designer">Product Designer</option>
                        <option value="ux-designer">UX Designer</option>
                        <option value="receptionist">Receptionist</option>
                        <option value="hr">HR</option>
                    </select>
                </div>
                <div class="career-openings__filter">
                    <label class="sr-only" for="career-job-type">Job Type</label>
                    <select id="career-job-type" name="job_type">
                        <option value="">Job Type</option>
                        <option value="full-time">Full Time</option>
                        <option value="temporary">Temporary</option>
                    </select>
                </div>
                <div class="career-openings__filter">
                    <label class="sr-only" for="career-location">Locations</label>
                    <select id="career-location" name="location">
                        <option value="">Locations</option>
                        <option value="dubai-uae">Dubai, UAE</option>
                        <option value="sharjah-uae">Sharjah, UAE</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-theme">Search</button>
            </form>

            <div class="career-openings__list" role="list">
                <article class="career-job-card" role="listitem">
                    <div class="career-job-card__top">
                    <h3>Product Designer 
                        <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/>
                        </svg>
                        Design
                    </span></h3>
                        <p>We're looking for a mid-level product designer to join our team.</p>
                    </div>
                    <ul class="career-job-card__meta">
                        <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
  <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            Full Time
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Temporary
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            13-05-2025
                        </li>
                        <li><img src="public/images/flag.png" alt="" width="16" height="16" loading="lazy"> Dubai,UAE</li>
                    </ul>
                    <a href="career-details.php" class="career-job-card__apply"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
  <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="black" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>Apply Now</a>
                </article>

                <article class="career-job-card" role="listitem">
                    <div class="career-job-card__top">
                        <h3>UX Designer     <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/>
                        </svg>
                        Design
                    </span></h3>
                        <p>We're looking for a mid-level product designer to join our team.</p>
                    </div>
                    <ul class="career-job-card__meta">
                        <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
  <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            Full Time
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Temporary
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            13-05-2025
                        </li>
                        <li><img src="public/images/flag.png" alt="" width="16" height="16" loading="lazy"> Dubai,UAE</li>
                    </ul>
                    <a href="career-details.php" class="career-job-card__apply"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
  <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="black" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>Apply Now</a>
                </article>

                <article class="career-job-card" role="listitem">
                    <div class="career-job-card__top">
                        <h3>Receptionist     <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                        <circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/>
                        </svg>
                        Design
                    </span></h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
                    </div>
                    <ul class="career-job-card__meta">
                        <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
  <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            Full Time
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Temporary
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            13-05-2025
                        </li>
                        <li><img src="public/images/flag.png" alt="" width="16" height="16" loading="lazy"> Dubai,UAE</li>
                    </ul>
                    <a href="career-details.php" class="career-job-card__apply"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
  <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="black" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>Apply Now</a>
                </article>

                <article class="career-job-card" role="listitem">
                    <div class="career-job-card__top">
                        <h3>HR <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
  <circle cx="5" cy="5.00012" r="5" fill="#A8A8A8"/>
</svg>Office</span></h3>
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting</p>
                    </div>
                    <ul class="career-job-card__meta">
                        <li>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
  <path d="M9 15.75C12.7279 15.75 15.75 12.7279 15.75 9C15.75 5.27208 12.7279 2.25 9 2.25C5.27208 2.25 2.25 5.27208 2.25 9C2.25 12.7279 5.27208 15.75 9 15.75Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M8.25 6V9.75H12" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                            Full Time
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12 5.25C12 3.8355 12 3.129 11.5605 2.6895C11.121 2.25 10.4145 2.25 9 2.25C7.5855 2.25 6.879 2.25 6.4395 2.6895C6 3.129 6 3.8355 6 5.25M1.5 10.5C1.5 8.39325 1.5 7.34025 2.0055 6.5835C2.22443 6.25579 2.50579 5.97443 2.8335 5.7555C3.59025 5.25 4.6425 5.25 6.75 5.25H11.25C13.3568 5.25 14.4098 5.25 15.1665 5.7555C15.4942 5.97443 15.7756 6.25579 15.9945 6.5835C16.5 7.34025 16.5 8.3925 16.5 10.5C16.5 12.6075 16.5 13.6598 15.9945 14.4165C15.7756 14.7442 15.4942 15.0256 15.1665 15.2445C14.4098 15.75 13.3575 15.75 11.25 15.75H6.75C4.64325 15.75 3.59025 15.75 2.8335 15.2445C2.50579 15.0256 2.22443 14.7442 2.0055 14.4165C1.5 13.6598 1.5 12.6075 1.5 10.5Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4.5 8.25L4.989 8.4015C7.60333 9.20039 10.3967 9.20039 13.011 8.4015L13.5 8.25M9 9V10.5" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Temporary
                        </li>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                                <path d="M12.375 3.75V2.25M5.625 3.75V2.25M2.4375 6H15.5625M2.25 7.533C2.25 5.94675 2.25 5.15325 2.577 4.54725C2.87268 4.00673 3.3315 3.57338 3.888 3.309C4.53 3 5.37 3 7.05 3H10.95C12.63 3 13.47 3 14.112 3.309C14.6768 3.5805 15.135 4.014 15.423 4.5465C15.75 5.154 15.75 5.9475 15.75 7.53375V11.2177C15.75 12.804 15.75 13.5975 15.423 14.2035C15.1273 14.744 14.6685 15.1774 14.112 15.4418C13.47 15.75 12.63 15.75 10.95 15.75H7.05C5.37 15.75 4.53 15.75 3.888 15.441C3.33161 15.1768 2.87281 14.7437 2.577 14.2035C2.25 13.596 2.25 12.8025 2.25 11.2162V7.533Z" stroke="#0A1119" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            13-05-2025
                        </li>
                        <li><img src="public/images/flag.png" alt="" width="16" height="16" loading="lazy"> Dubai,UAE</li>
                    </ul>
                    <a href="career-details.php" class="career-job-card__apply"> <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26" fill="none">
  <path d="M9.28234 17.1303L15.8309 7.98271M15.8309 7.98271L9.71616 8.79363M15.8309 7.98271L17.0342 14.0325" stroke="black" stroke-width="1.125" stroke-linecap="round" stroke-linejoin="round"/>
</svg> Apply Now</a>
                </article>
            </div>
        </div>
    </section>


<?php
require __DIR__ . '/includes/footer.php';
require __DIR__ . '/includes/modal-enquiry.php';

require __DIR__ . '/includes/floating-widgets.php';
require __DIR__ . '/includes/scripts.php';
