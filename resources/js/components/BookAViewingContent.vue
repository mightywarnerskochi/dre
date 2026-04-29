<template>
<section class="banner banner--page banner--page--listing position-relative" aria-label="Page header">
        <div class="banner--page__bg">
            <picture>
                <img :src="asset('public/images/inner-banner.jpg')" alt="" width="1920" height="403" loading="eager">
            </picture>
        </div>
        <div class="banner--page__content">
            <div class="container-ctn">
                <h1 class="banner--page__title">Book a Viewing</h1>
                <ol class="breadcrumb-minimal" aria-label="Breadcrumb">
                    <li><a href="/">Home</a></li>
                    <li class="breadcrumb-minimal__sep" aria-hidden="true">/</li>
                    <li class="breadcrumb-minimal__current" aria-current="page">Book a Viewing</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="book-viewing-form commonPadding-120" aria-labelledby="book-viewing-title">
        <div class="container-ctn">
            <h2 id="book-viewing-title" class="sr-only">Book a Viewing Form</h2>
            <div class="book-viewing-form__inner">
                <div class="book-viewing-form__panel">
                    <form action="" method="post" autocomplete="on">
                        <div class="book-viewing-form__grid">
                            <input class="book-viewing-form__field" type="text" name="name" placeholder="Name" autocomplete="name">
                            <input class="book-viewing-form__field" type="email" name="email" placeholder="Email" autocomplete="email">
                            <input class="book-viewing-form__field" type="tel" name="phone" placeholder="Phone" autocomplete="tel">

                            <div class="d-flex justify-content-between ">
                            <div class="book-viewing-form__field-wrap book-viewing-form__field-wrap--date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M16.5 5V3M7.5 5V3M3.25 8H20.75M3 10.044C3 7.929 3 6.871 3.436 6.063C3.83025 5.34231 4.44199 4.7645 5.184 4.412C6.04 4 7.16 4 9.4 4H14.6C16.84 4 17.96 4 18.816 4.412C19.569 4.774 20.18 5.352 20.564 6.062C21 6.872 21 7.93 21 10.045V14.957C21 17.072 21 18.13 20.564 18.938C20.1698 19.6587 19.558 20.2365 18.816 20.589C17.96 21 16.84 21 14.6 21H9.4C7.16 21 6.04 21 5.184 20.588C4.44214 20.2358 3.83041 19.6583 3.436 18.938C3 18.128 3 17.07 3 14.955V10.044Z" stroke="#4B535D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                                <input class="book-viewing-form__field" type="text" name="date" placeholder="Date" onfocus="this.type='date'" onblur="if(!this.value){this.type='text'}">
                            </div>

                            <div class="book-viewing-form__field-wrap book-viewing-form__field-wrap--time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M15.098 12.634L13 11.423V7C13 6.73478 12.8946 6.48043 12.7071 6.29289C12.5196 6.10536 12.2652 6 12 6C11.7348 6 11.4804 6.10536 11.2929 6.29289C11.1054 6.48043 11 6.73478 11 7V12C11 12.1755 11.0462 12.348 11.134 12.5C11.2218 12.652 11.348 12.7782 11.5 12.866L14.098 14.366C14.2118 14.4327 14.3376 14.4762 14.4683 14.4941C14.5989 14.512 14.7319 14.5038 14.8594 14.4701C14.9869 14.4364 15.1064 14.3778 15.2112 14.2977C15.3159 14.2176 15.4038 14.1175 15.4698 14.0033C15.5357 13.8891 15.5784 13.763 15.5954 13.6322C15.6124 13.5014 15.6034 13.3686 15.5688 13.2413C15.5343 13.114 15.4749 12.9949 15.3941 12.8906C15.3133 12.7864 15.2127 12.6992 15.098 12.634ZM12 2C10.0222 2 8.08879 2.58649 6.4443 3.6853C4.79981 4.78412 3.51809 6.3459 2.76121 8.17317C2.00433 10.0004 1.8063 12.0111 2.19215 13.9509C2.578 15.8907 3.53041 17.6725 4.92894 19.0711C6.32746 20.4696 8.10929 21.422 10.0491 21.8079C11.9889 22.1937 13.9996 21.9957 15.8268 21.2388C17.6541 20.4819 19.2159 19.2002 20.3147 17.5557C21.4135 15.9112 22 13.9778 22 12C21.9974 9.34865 20.9429 6.80665 19.0681 4.93186C17.1934 3.05707 14.6514 2.00265 12 2ZM12 20C10.4178 20 8.87104 19.5308 7.55544 18.6518C6.23985 17.7727 5.21447 16.5233 4.60897 15.0615C4.00347 13.5997 3.84504 11.9911 4.15372 10.4393C4.4624 8.88743 5.22433 7.46197 6.34315 6.34315C7.46197 5.22433 8.88743 4.4624 10.4393 4.15372C11.9911 3.84504 13.5997 4.00346 15.0615 4.60896C16.5233 5.21447 17.7727 6.23984 18.6518 7.55544C19.5308 8.87103 20 10.4177 20 12C19.9974 14.1209 19.1537 16.1542 17.6539 17.6539C16.1542 19.1536 14.1209 19.9974 12 20Z" fill="#4B535D"/>
                            </svg>
                                <select class="book-viewing-form__field" name="time" aria-label="Preferred time">
                                    <option value="" selected disabled>Time</option>
                                    <option value="09:00">09:00 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="12:00">12:00 PM</option>
                                    <option value="13:00">01:00 PM</option>
                                    <option value="14:00">02:00 PM</option>
                                    <option value="15:00">03:00 PM</option>
                                    <option value="16:00">04:00 PM</option>
                                    <option value="17:00">05:00 PM</option>
                                    <option value="18:00">06:00 PM</option>
                                </select>
                                <span class="book-viewing-form__select-arrow" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M3.5 5.5L7 9L10.5 5.5" stroke="#4B535D" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </div>
                            </div>

                            <div class="book-viewing-form__textarea-wrap w-100">
                                <textarea class="book-viewing-form__field book-viewing-form__textarea" name="message" placeholder="Message"></textarea>
                            </div>
                        </div>
                        <div class="book-viewing-form__actions">
                            <button type="submit" class="btn btn-theme book-viewing-form__submit">Submit</button>
                            <button type="reset" class="book-viewing-form__reset">Refresh</button>
                        </div>
                    </form>
                </div>

                <figure class="book-viewing-form__media">
                    <img :src="asset('public/images/book-a-view.jpg')" alt="Residential community view in Dubai" width="720" height="620" loading="lazy">
                </figure>
            </div>
        </div>
    </section>

</template>

<script setup>
import { asset } from '@/utils/asset';
</script>
