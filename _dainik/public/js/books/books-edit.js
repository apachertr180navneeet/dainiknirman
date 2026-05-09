var Books = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Books.validateForm();
            Books.initializeComponents();
            Books.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#edit-book-form');
            
            // Bootstrap Select
            Components.bootstrapSelect($form);
            //--------------

            // Image preview
            Components.imagePreview($form);
            //--------------

            // Description Editor
            Components.descriptionEditor($form);
            //-------------------

            // Date Range picker
            var $date_range_picker = $form.find(".date-picker");

            if ($date_range_picker.length) {
                $date_range_picker.flatpickr({
                    dateFormat: "d-m-Y",
                    // mode: "range",
                    // maxDate: "today",
                });
            }
            //----------------
        },

        /**
         * Custom validation methods.
         */
        customValidationMethods: function() {
            jQuery.validator.addMethod(
                "lettersOnly",
                function(value, element) {
                    return (
                        this.optional(element) ||
                        /^[a-zA-Z][a-zA-Z ]+$/i.test(value)
                    );
                },
                "Please enter only alphabets."
            );

            jQuery.validator.addMethod(
                "numericOnly",
                function(value, element) {
                    return (
                        this.optional(element) ||
                        /^[0-9]\d{0,1}(\.\d{1,2})?%?$/i.test(value)
                    );
                },
                "Please enter valid number."
            );

            jQuery.validator.addMethod(
                "uppercaseOnly",
                function(value, element) {
                    return (
                        this.optional(element) ||
                        /^[A-Z]+$/g.test(value)
                    );
                },
                "Please enter only capital letters."
            );

            jQuery.validator.addMethod(
                "emailChecker",
                function(value, element) {
                    return (
                        this.optional(element) ||
                        /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i.test(
                            value
                        )
                    );
                },
                "Please enter a valid email address."
            );
        },

        /**
         * Validate country form.
         */
        validateForm: function() {
            var $form = $("#edit-book-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    distributor_id: {
                        required: true
                    },
                    agent_id: {
                        required: true
                    },
                    name: {
                        required: true,
                    },
                    mobile: {
                        required: false,
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        // remote: {
                        //     url: $("#mobile").data("check-url"),
                        //     type: "post",
                        //     data: {
                        //         mobile: function() {
                        //             return $("#mobile").val();
                        //         },
                        //         customer_id: function() {
                        //             return $("#customer_id").val();
                        //         },
                        //     }
                        // }
                    },
                    email: {
                        email: true,
                        required: false,
                        // remote: {
                        //     url: $("#email").data("check-url"),
                        //     type: "post",
                        //     data: {
                        //         email: function() {
                        //             return $("#email").val();
                        //         },
                        //         customer_id: function() {
                        //             return $("#customer_id").val();
                        //         },
                        //     }
                        // }
                    },
                    occupation:{
                        required: false
                    },
                    aadhar_number: {
                        required: false
                    },
                    // pan_card_photo: {
                    //     required: {
                    //         depends: function(){
                    //             if(($("#pan_card_photo").attr("data-default-file") === undefined || $("#pan_card_photo").attr("data-default-file") == '')){
                    //                 return true;
                    //             }
                    //         }
                    //     },
                    //     accept: "image/jpg, image/jpeg, image/png, image/gif"
                    // },
                    // aadhar_card_photo: {
                    //     required: {
                    //         depends: function(elem){
                    //             if(($("#aadhar_card_photo").attr("data-default-file") === undefined || $("#aadhar_card_photo").attr("data-default-file") == '')){
                    //                 return true;
                    //             }
                    //         }
                    //     },
                    //     accept: "image/jpg, image/jpeg, image/png, image/gif"
                    // },
                    address:{
                        required: false
                    },
                    city_name: {
                        required: false,
                    },
                    state_name: {
                        required: false,
                    },
                    pincode: {
                        required: false,
                        maxlength: 6,
                        digits: true,
                    },
                    bank_name: {
                        required: false
                    },
                    ifsc_code: {
                        required: false
                    },
                    bank_account_type: {
                        required: false
                    },
                    bank_account_number: {
                        required: false,
                        digits: true,
                        remote: {
                            url: $("#bank_account_number").data("check-url"),
                            type: "post",
                            data: {
                                bank_account_number: function() {
                                    return $("#bank_account_number").val();
                                },
                                customer_id: function() {
                                    return $("#customer_id").val();
                                },
                            }
                        }
                    },
                    firm_name: {
                        required: false,
                        remote: {
                            url: $("#firm_name").data("check-url"),
                            type: "post",
                            data: {
                                firm_name: function() {
                                    return $("#firm_name").val();
                                },
                                customer_id: function() {
                                    return $("#customer_id").val();
                                },
                            }
                        }
                    },
                    role_id: {
                        required: false,
                    },
                    pan_number: {
                        required: true,
                        remote: {
                            url: $("#pan_number").data("check-url"),
                            type: "post",
                            data: {
                                pan_number: function() {
                                    return $("#pan_number").val();
                                },
                                customer_id: function() {
                                    return $("#customer_id").val();
                                },
                            }
                        }
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    distributor_id: {
                        required: "This field is required."
                    },
                    agent_id: {
                        required: "This field is required."
                    },
                    name: {
                        required: "This field is required."
                    },
                    mobile: {
                        required: "This field is required.",
                        minlength: "Mobile no. must be at leat 10 digits long.",
                        maxlength: "Mobile no. cannot be more than 10 digits.",
                        digits: "Please enter valid mobile number.",
                        remote: "The mobile number already exists."
                    },
                    email: {
                        email: "Please enter a valid email.",
                        required: "This field is required.",
                        remote: "The email already exists."
                    },
                    occupation:{
                        required: "This field is required."
                    },
                    pan_number: {
                        required: "This field is required."
                    },
                    aadhar_number: {
                        required: "This field is required."
                    },
                    pan_card_photo: {
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    aadhar_card_photo: {
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    address:{
                        required: "This field is required."
                    },
                    city_name: {
                        required: "This field is required.",
                    },
                    state_name: {
                        required: "This field is required.",
                    },
                    pincode: {
                        required: "This field is required.",
                        maxlength: "Pincode cannot be more than 6 digits.",
                        digits: "Please provide a valid pincode format.",
                    },
                    bank_name: {
                        required: "This field is required."
                    },
                    ifsc_code: {
                        required: "This field is required."
                    },
                    bank_account_type: {
                        required: "This field is required."
                    },
                    bank_account_number: {
                        required: "This field is required.",
                        digits: "Please provide a valid bank account number.",
                        remote: "The bank account number already exist."
                    },
                    firm_name: {
                        required: "This field is required.",
                        remote: "The firm name already exists."
                    },
                    role_id: {
                        required: "This field is required.",
                    },
                    pan_number: {
                        required: "This field is required.",
                        remote: "The pan number already exists."
                    },
                },
                //---------------------------

                highlight: function(element, errorClass, validClass) {
                    $(element)
                        .closest(".form-group")
                        .addClass("has-danger")
                        .removeClass("has-success");
                    $(element)
                        .addClass("is-invalid")
                        .removeClass("is-valid");
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element)
                        .closest(".form-group")
                        .addClass("has-success")
                        .removeClass("has-danger");
                    $(element)
                        .addClass("is-valid")
                        .removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    if($(element).hasClass('select-picker'))
                    {
                        $(element).on('change', function(){
                            $(this).valid();
                        });
                        
                        error.appendTo($(element).parent().parent());
                    }
                    else if($(element).hasClass('image-preview'))
                    {
                        error.appendTo($(element).parents('.dropify-wrapper').parent());
                    }
                    else
                    {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    App.formLoading($form);
                    form.submit();
                }
            });
        }
    };
})();

Books.init();