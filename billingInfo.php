<?php

include('check-session.php');



?>

<style>
    .gridForm {
        gap: 5px 10px;
    }

    .billingInfoPage .themeBtn2grey {
        gap: 4px;
    }

    .gridForm label {
        margin-bottom: 4px;
    }

    .billingInfoPage .themeBtn2grey {
        width: 100% !important;
    }

    /* Required field styling */
    .required-star {
        color: #dc3545;
        font-weight: bold;
        border: none !important;
    }

    .form-group.required label {
        font-weight: 600;
    }

    .fa-exclamation-triangle {
        display: none;
        color: #dc3545;
        margin-left: 5px;
    }

    .input-error {
        border-color: #dc3545 !important;
    }

    .validation-message {
        color: #dc3545;
        font-size: 0.85em;
        display: none;
        margin-top: 3px;
    }
</style>

<div class="container-fluid p-0 h-100 billingInfoPage">

    <div class="innerMainContent">

        <div class=" defaultHeader position-relative">

            <div class="pageHeader">

                <h2>Billing Info</h2>

                <p>Select billing and delivery address for your orders. </p>
            </div>

            <div class="goBackBtn  " id="newUserLink">

                <button type="button" class="btn themeBtn2 iconBTn XSmall" onclick="return prepareNewAddress();">

                    <figure class="m-0"><img src="images/vector/add.png" alt=""></figure> Address

                </button>



            </div>

        </div>



        <div class="billingInfoTable p-0   ">

            <div class="toast w-100 bg-none" id="myToast" role="alert" aria-live="assertive" aria-atomic="true"

                data-bs-delay="500000">

                <div class="toast-header text-start">

                    <p class="m-0 text-start d-flex gap-3">

                    <figure class="mx-2"><img src="images/vector/notification.png" alt=""></figure> On

                    this page you

                    can edit your billing information as well as add and

                    maintain a list of

                    new

                    / additional addresses for billing or delivery. When you are ready to start a new order

                    please select the appropriate addresses: one for billing and one for delivery address to

                    populate your new order form.</p>

                    <figure><img src="images/vector/close.png" alt="" class="btn-close" data-bs-dismiss="toast"

                            aria-label="Close"></figure>

                </div>

            </div>



        </div>

        <div class="billingInfoTable">

            <div class="row infoHeader">

                <div class="col-4   text-start borderRight">

                    <h6 class="XSmall m-0">Contact</h6>

                </div>

                <div class="col-4  text-start borderRight">

                    <h6 class="XSmall  ">Address</h6>

                </div>

                <div class="col-1 borderRight">

                    <h6 class="XSmall ">TAX ID</h6>

                </div>

                <div class="col-1 borderRight">

                    <h6 class="XSmall    ">Billing</h6>

                </div>

                <div class="col-1 borderRight">

                    <h6 class="XSmall    ">Delivery</h6>

                </div>

                <div class="col-1">

                    <h6 class="XSmall   ">Action</h6>

                </div>

            </div>

            <div class="    activeInfoBody " id="billing_addr_content">

            </div>

        </div>



        <div class="newAddressForm d-none" id="newUserForm">

            <div class="boxes">

                <form action="">

                    <div class="formTitle d-flex align-items-center flex-row">

                        <h6 class="Small m-0">Fill details of your new address. </h6>

                        <span class="sm-Btn" id="cancelBtn">

                            <figure class="m-0"><img src="images/vector/close2.png" alt=""></figure>

                        </span>

                    </div>



                    <fieldset class="grid2 singleFrom">

                        <div class="form-group column2">

                            <label for="">Company</label>

                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>

                        <div class="form-group column2">

                            <label for="">Contact</label>
                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>



                        <div class="form-group column2">

                            <label for="">Address</label>

                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>



                        <div class="form-group">

                            <label for="">City</label>

                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>

                        <div class="form-group">

                            <label for="">ZipCode</label>

                            <input type="text" name="" value=" " placeholder=" ">

                        </div>

                        <div class="form-group column2">

                            <label for="">Email</label>

                            <input type="email" name=" " value=" " placeholder=" ">

                        </div>

                        <div class="form-group column2">

                            <label for="">Tel.</label>



                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>

                        <div class="form-group column2">

                            <label for="">TAX-ID</label>

                            <input type="text" name=" " value=" " placeholder=" ">

                        </div>

                    </fieldset>



                    <div class="formTitle d-flex align-items-center flex-row">

                        <div class="grid2">

                            <div class="goBackBtn themeBtn2grey">

                                <a href="#" class="goback switch-tab" data-target="#order">Go

                                    Cancel</a>

                            </div>

                            <a href="#" class="themeBtn switch-tab" data-target="#order">Save and

                                Submit</a>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <div class="main-content-footer">

        <a href="">Copyright © 2020 JOGSPORTS. All rights reserved. </a>

    </div>

</div>

<!-- Modal -->

<div class="modal fade" id="newAddressModal" tabindex="-1" aria-labelledby="modal_form_title" aria-hidden="true">

    <div class="modal-dialog smallModal">

        <div class="modal-content">

            <div class="modal-header">

                <h1 class="modal-title fs-5" id="modal_form_title">Add New Address</h1>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <form class="addr_add_form" id="form_new_addr">

                    <div class="row">

                        <div class="grid2 gridForm">

                            <div class="form-group">

                                <label for=""> Company</label>

                                <input type="text" name="company_name" id="edit_company_name" class="form-control" maxlength="150" placeholder="Add Company Name..">

                                <i id="req_company_name" class="fa fa-exclamation-triangle"></i>

                            </div>

                            <div class="form-group required">

                                <label for=""> Contact <span class="required-star">*</span></label>

                                <input type="tel" name="contact" id="edit_contact" class="form-control" maxlength="30" placeholder="Add Contact Name">
                                <div id="error_contact" class="validation-message"></div>

                            </div>

                            <div class="form-group required">

                                <label for=""> City <span class="required-star">*</span></label>

                                <input type="text" name="city" id="edit_city" class="form-control" maxlength="80" placeholder="Add Your City..">
                                <div id="error_city" class="validation-message"></div>

                            </div>

                            <div class="form-group required">

                                <label for=""> Country <span class="required-star">*</span></label>

                                <input type="text" name="country" id="edit_country" class="form-control" maxlength="50" placeholder="Add Your Country..">
                                <div id="error_country" class="validation-message"></div>

                            </div>

                            <div class="form-group required">

                                <label for=""> Zipcode <span class="required-star">*</span></label>

                                <input type="number" name="zipcode" id="edit_zipcode" class="form-control" maxlength="20" placeholder="Add Zip Code..">
                                <div id="error_zipcode" class="validation-message"></div>

                            </div>

                            <div class="form-group required">

                                <label for=""> Tel. <span class="required-star">*</span></label>

                                <input type="tel" name="tel" id="edit_tel" class="form-control" maxlength="30" placeholder="Add Telephone Number..">
                                <div id="error_tel" class="validation-message"></div>

                            </div>

                            <div class="form-group required">

                                <label for=""> Email <span class="required-star">*</span></label>

                                <input type="email" name="email_info" id="edit_email_info" class="form-control" maxlength="200" placeholder="Add Your Email..">
                                <div id="error_email_info" class="validation-message"></div>

                            </div>

                            <div class="form-group">

                                <label for=""> TAX ID</label>

                                <input type="text" name="tax_no" id="edit_tax_no" class="form-control" maxlength="30" placeholder="Add Your TAX -ID..">

                                <input type="hidden" name="edit_addr_id" id="edit_addr_id">

                            </div>

                            <div class="form-group column2 required">

                                <label for=""> Address <span class="required-star">*</span></label>

                                <textarea name="address_info" id="edit_address_info" class="form-control" rows="3"></textarea>
                                <div id="error_address_info" class="validation-message"></div>

                            </div>

                        </div>

                    </div>

                </form>


            </div>

            <div class="modal-footer">
                <button type="button" class="btn iconBTn themeBtn2grey" data-bs-dismiss="modal">
                    <figure class="m-0"><img src="images/vector/cancel.png" alt=""></figure> Close
                </button>

                <button type="button" class="btn iconBTn themeBtn" id="btn_submit_address" onclick="return saveAddressInfo();">
                    Submit <figure class="m-0"><img src="images/vector/nextBtn.png" alt=""></figure>
                </button>
            </div>

        </div>

    </div>

</div>


<script type="text/javascript">
    showBillingInfo();

    // Validation patterns
    const VALIDATION_PATTERNS = {
        contact: /^[0-9+\-\s]{3,30}$/, // Numbers, +, -, spaces
        city: /^[a-zA-Z\s]{2,80}$/, // Letters and spaces only
        country: /^[a-zA-Z\s]{2,50}$/, // Letters and spaces only
        zipcode: /^[a-zA-Z0-9\-\s]{2,20}$/, // Letters, numbers, hyphens
        tel: /^[0-9+\-\s\(\)]{3,30}$/, // Numbers, +, -, spaces, parentheses
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ // Standard email format
    };

    // Clear all validation errors
    function clearValidationErrors() {
        $('.fa-exclamation-triangle').hide();
        $('.validation-message').hide().text('');
        $('input, textarea').removeClass('input-error');
    }


    // Show validation error for a field
    function showFieldError(fieldId, message) {
        $('#req_' + fieldId).show();
        $('#error_' + fieldId).text(message).show();
        $('#edit_' + fieldId).addClass('input-error');
    }

    // Clear validation error for a field
    function clearFieldError(fieldId) {
        $('#req_' + fieldId).hide();
        $('#error_' + fieldId).hide().text('');
        $('#edit_' + fieldId).removeClass('input-error');
    }


    // Validate single field
    function validateField(fieldId, value, pattern, requiredMsg, formatMsg) {
        if (value === "" || value === null || value === undefined) {
            showFieldError(fieldId, requiredMsg);
            return false;
        }
        if (pattern && !pattern.test(value)) {
            showFieldError(fieldId, formatMsg);
            return false;
        }
        clearFieldError(fieldId);
        return true;
    }


    function checkValue() {
        clearValidationErrors();

        var isValid = true;

        // Validate Contact (required, phone format)
        var contact = $('#edit_contact').val().trim();
        if (!validateField('contact', contact, VALIDATION_PATTERNS.city,
                'Contact name is required.',
                'Invalid contact name. Use only letter and spaces')) {
            isValid = false;
        }

        // Validate Address (required)
        var address = $('#edit_address_info').val().trim();
        if (!validateField('address_info', address, null,
                'Address is required.', '')) {
            isValid = false;
        }

        // Validate City (required, letters only)
        var city = $('#edit_city').val().trim();
        if (!validateField('city', city, VALIDATION_PATTERNS.city,
                'City is required.',
                'City should contain only letters and spaces.')) {
            isValid = false;
        }

        // Validate Country (required, letters only)
        var country = $('#edit_country').val().trim();
        if (!validateField('country', country, VALIDATION_PATTERNS.country,
                'Country is required.',
                'Country should contain only letters and spaces.')) {
            isValid = false;
        }

        // Validate Zipcode (required, alphanumeric)
        var zipcode = $('#edit_zipcode').val().trim();
        if (!validateField('zipcode', zipcode, VALIDATION_PATTERNS.zipcode,
                'Zipcode is required.',
                'Zipcode should contain only letters, numbers, and hyphens.')) {
            isValid = false;
        }

        // Validate Tel (required, phone format)
        var tel = $('#edit_tel').val().trim();
        if (!validateField('tel', tel, VALIDATION_PATTERNS.tel,
                'Telephone number is required.',
                'Invalid telephone number. Use only numbers, +, -, spaces, and parentheses.')) {
            isValid = false;
        }

        // Validate Email (required, email format)
        var email = $('#edit_email_info').val().trim();
        if (!validateField('email_info', email, VALIDATION_PATTERNS.email,
                'Email is required.',
                'Please enter a valid email address.')) {
            isValid = false;
        }

        return isValid;
    }


    // Real-time validation on blur
    $(document).ready(function() {
        $('#edit_contact').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.contact.test(val)) {
                showFieldError('contact', 'Invalid contact number. Use only numbers, +, -, and spaces.');
            } else {
                clearFieldError('contact');
            }
        });

        $('#edit_city').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.city.test(val)) {
                showFieldError('city', 'City should contain only letters and spaces.');
            } else {
                clearFieldError('city');
            }
        });

        $('#edit_country').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.country.test(val)) {
                showFieldError('country', 'Country should contain only letters and spaces.');
            } else {
                clearFieldError('country');
            }
        });

        $('#edit_zipcode').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.zipcode.test(val)) {
                showFieldError('zipcode', 'Zipcode should contain only letters, numbers, and hyphens.');
            } else {
                clearFieldError('zipcode');
            }
        });

        $('#edit_tel').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.tel.test(val)) {
                showFieldError('tel', 'Invalid telephone number. Use only numbers, +, -, spaces, and parentheses.');
            } else {
                clearFieldError('tel');
            }
        });

        $('#edit_email_info').on('blur', function() {
            var val = $(this).val().trim();
            if (val && !VALIDATION_PATTERNS.email.test(val)) {
                showFieldError('email_info', 'Please enter a valid email address.');
            } else {
                clearFieldError('email_info');
            }
        });

        $('#edit_address_info').on('blur', function() {
            var val = $(this).val().trim();
            if (!val) {
                showFieldError('address_info', 'Address is required.');
            } else {
                clearFieldError('address_info');
            }
        });
    });


    function editAddrInfo(addr_id) {
        $('#newAddressModal').modal('show');

        $('#modal_form_title').html('Edit Address');

        $('#btn_submit_address').attr("onclick", "return saveEditAddressInfo();");



        $('.fa-exclamation-triangle').hide();



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/billing/get_billing_info.php",

            data: {

                "addr_id": addr_id

            },

            success: function(resp) {



                if (resp.result == "success") {
                    $('#edit_addr_id').val(addr_id);
                    $('#edit_company_name').val(resp.company_name);
                    $('#edit_contact').val(resp.contact);
                    $('#edit_address_info').val(resp.address_info);
                    $('#edit_city').val(resp.city);
                    $('#edit_country').val(resp.country);
                    $('#edit_zipcode').val(resp.zipcode);
                    $('#edit_tel').val(resp.tel);
                    $('#edit_email_info').val(resp.email_info);
                    $('#edit_tax_no').val(resp.tax_no);
                } else {

                    alert(resp.msg);

                }

            }

        });



    }



    function saveEditAddressInfo() {
        if (checkValue()) {

            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/billing/save_edit_billing_info.php",

                data: $('#form_new_addr').serialize(),

                success: function(resp) {



                    if (resp.result == "saved") {



                        showBillingInfo();

                        $('#form_new_addr').trigger("reset");
                        $('#newAddressModal').modal('hide');



                    } else {

                        alert(resp.msg);

                    }

                }

            });
        } else {

            alert("Please input required info.");

        }

    }



    function prepareNewAddress() {

        $('#newAddressModal').modal('show');
        $('#modal_form_title').html('Add New Address');

        $('#btn_submit_address').attr("onclick", "return saveNewAddressInfo();");

        $('#form_new_addr').trigger("reset");
        $('.fa-exclamation-triangle').hide();

    }
 


    function saveNewAddressInfo() {

        if (checkValue()) {
            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/billing/save_billing_info.php",

                data: $('#form_new_addr').serialize(),

                success: function(resp) {

                    if (resp.result == "saved") {
                        showBillingInfo();

                        $('#form_new_addr').trigger("reset");
                        $('#newAddressModal').modal("hide");




                    } else {

                        alert(resp.msg);

                    }

                }

            });



        } else {

            alert("Please input required info.");

        }

    }



    function showBillingInfo() {



        $('#billing_addr_content').html('<i class="fa fa-cog fa-spin fa-1x fa-fw"></i> Loding...');



        $.ajax({

            type: "POST",

            dataType: "html",

            url: "ajax/billing/show_billing_info.php",

            success: function(resp) {



                $('#billing_addr_content').html(resp);



            }

        });



    }



    function deleteAddrInfo(addr_id) {



        if (confirm("Removing Address. Confirm?")) {

            $.ajax({

                type: "POST",

                dataType: "json",

                url: "ajax/billing/delete_info.php",

                data: {

                    "addr_id": addr_id

                },

                success: function(resp) {



                    if (resp.result == "success") {



                        showBillingInfo();



                    } else {

                        alert(resp.msg);

                    }

                }

            });

        }



    }



    function setDefaultBilling(addr_id) {

        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/billing/set_default_billing.php",

            data: {

                "addr_id": addr_id

            },

            success: function(resp) {
                if (resp.result != "success") {
                    alert(resp.msg);

                }

            }

        });



    }



    function setDefaultDeliver(addr_id) {



        $.ajax({

            type: "POST",

            dataType: "json",

            url: "ajax/billing/set_default_deliver.php",

            data: {

                "addr_id": addr_id

            },

            success: function(resp) {



                if (resp.result != "success") {

                    alert(resp.msg);

                }

            }

        });



    }
</script>