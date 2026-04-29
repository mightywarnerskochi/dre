    <!-- Enquiry Modal -->
    <div class="modal fade siteEnquiryForm" id="siteEnquiryForm" aria-hidden="true"
        aria-labelledby="siteEnquiryFormLabel" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    id="siteEnquiryFormClose">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                        <path d="M1.25 15.3933L15.3933 1.25M15.3933 15.3933L1.25 1.25" stroke="black" stroke-width="2.5"
                            stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="modal-body p-0">
                    <form action="" class="enquiry-form border-0 bg-white">
                        <h3 class="mb-4">Enquire Now</h3>

                        <div class="form-row d-flex flex-wrap">
                            <div class="form-group">
                                <input type="text" name="name" placeholder="Name">
                            </div>
                            <div class="form-group">
                                <input type="tel" name="phone" class="phone_number" placeholder="Phone">
                            </div>

                            <div class="form-group">
                                <input type="email" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <input type="text" name="location" placeholder="Property Location">
                            </div>

                            <div class="form-group">
                                <select name="property_type">
                                    <option value="" disabled selected hidden>Property Type</option>
                                    <option value="apartment">Apartment</option>
                                    <option value="villa">Villa</option>
                                    <option value="townhouse">Townhouse</option>
                                    <option value="plot">Plot</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="property_size" placeholder="Property Size">
                            </div>



                        </div>

                        <div class="form-action">
                            <button type="submit" class="btn-theme">Send Enquiry</button>
                        </div>

                        <div class="form-footer">
                            <p>Our team will contact you within 24 hours.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
