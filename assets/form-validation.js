var FormValidation = function () {

    var handleValidationProfile = function() {
        // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var profile = $('#form_profile');
            var error_profile = $('.alert-error-profile', profile);

            profile.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    firstname: {
                        minlength: 2,
                        required: true
                    },
                    middlename: {
                        minlength: 2,
                        required: true
                    },
                    lastname: {
                        minlength: 2,
                        required: true
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit       
                    error_profile.show();
                    FormValidation.scrollTo(error_profile, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid').addClass('help-inline ok') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                },

                submitHandler: function (form) {
                    form.submit();
                }
            });
    }

    var handleValidationPassword = function() {
        // for more info visit the official plugin documentation: 
            // http://docs.jquery.com/Plugins/Validation

            var password = $('#form_password');
            var error_password = $('.alert-error', password);

            password.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    pw1: {
                        minlength: 7,
                        maxlength:20,
                        required: true
                    },
                    pw2: {
                        minlength: 7,
                        maxlength:20,
                        required: true,
                        equalTo:"#pw1"
                    },
                    pw3: {
                        minlength: 7,
                        maxlength:20,
                        required: true
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    //success1.hide();
                    error_password.show();
                    FormValidation.scrollTo(error_password, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid').addClass('help-inline ok') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                },

                submitHandler: function (form) {
                    form.submit();
                    //success1.show();
                    //error_password.hide();
                }
            });
    }

    return {
        //main function to initiate the module
        init: function () {

            handleValidationProfile();
            handleValidationPassword();

        },

	// wrapper function to scroll to an element
        scrollTo: function (el, offeset) {
            pos = el ? el.offset().top : 0;
            jQuery('html,body').animate({
                    scrollTop: pos + (offeset ? offeset : 0)
                }, 'slow');
        }

    };

}();