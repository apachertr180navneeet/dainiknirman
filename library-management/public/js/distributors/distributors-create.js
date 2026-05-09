var Distributors = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Distributors.validateForm();
            Distributors.initializeComponents();
            Distributors.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#add-distributor-form');
            
            // Bootstrap Select
            Components.bootstrapSelect($form);
            //--------------

            // Image preview
            Components.imagePreview($form);
            //--------------

            // Description Editor
            Components.descriptionEditor($form);
            //-------------------
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
            var $form = $("#add-distributor-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    name: {
                        required: true,
                        // remote: {
                        //     url: $("#name").data("check-url"),
                        //     type: "post",
                        //     data: {
                        //         name: function() {
                        //             return $("#name").val();
                        //         }
                        //     }
                        // }
                    },
                    mobile: {
                        required: true,
                        minlength: 10,
                        maxlength: 10,
                        digits: true,
                        remote: {
                            url: $("#mobile").data("check-url"),
                            type: "post",
                            data: {
                                mobile: function() {
                                    return $("#mobile").val();
                                }
                            }
                        }
                    },
                    email: {
                        email: true,
                        required: true,
                        remote: {
                            url: $("#email").data("check-url"),
                            type: "post",
                            data: {
                                email: function() {
                                    return $("#email").val();
                                }
                            }
                        }
                    },
                    firm_name: {
                        required: true,
                        remote: {
                            url: $("#firm_name").data("check-url"),
                            type: "post",
                            data: {
                                firm_name: function() {
                                    return $("#firm_name").val();
                                }
                            }
                        }
                    },
                    role_id: {
                        required: true,
                    },
                    password: {
                        minlength: 6,
                        maxlength: 20,
                        required: true,
                    },
                    confirm_password: {
                        required: true,
                        equalTo: "#password"
                    },
                    distributor_code: {
                        required: true,
                        remote: {
                            url: $("#distributor_code").data("check-url"),
                            type: "post",
                            data: {
                                distributor_code: function() {
                                    return $("#distributor_code").val();
                                }
                            }
                        }
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    name: {
                        required: "This field is required.",
                        remote: "The distributor name already exists."
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
                    firm_name: {
                        required: "This field is required.",
                        remote: "The firm name already exists."
                    },
                    role_id: {
                        required: "This field is required.",
                    },
                    password: {
                        minlength: "Password must be at least 6 characters long.",
                        maxlength: "Password cannot be more than 20 characters.",
                        required: "This field is required.",
                    },
                    confirm_password: {
                        required: "This field is required.",
                        equalTo: "Confirm password must be same as password.",
                    },
                    distributor_code: {
                        required: "This field is required.",
                        remote: "The distributor code already exists."
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
        },
    };
})();

Distributors.init();