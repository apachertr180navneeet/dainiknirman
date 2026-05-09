var Users = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Users.validateForm();
            Users.initializeComponents();
            Users.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#add-user-form');
            
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
                    maxDate: "today",
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
            var $form = $("#add-user-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    name: {
                        required: true,
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
                    address:{
                        required: false
                    },
                    city_name: {
                        required: false,
                    },
                    bank_name: {
                        required: false
                    },
                    ifsc_code: {
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
                                }
                            }
                        }
                    }
                },
                //------------------

                // @validation error messages
                messages: {
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
                    address:{
                        required: "This field is required."
                    },
                    city_name: {
                        required: "This field is required.",
                    },
                    bank_name: {
                        required: "This field is required."
                    },
                    ifsc_code: {
                        required: "This field is required."
                    },
                    bank_account_number: {
                        required: "This field is required.",
                        digits: "Please provide a valid bank account number.",
                        remote: "The bank account number already exist."
                    }
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
        getAgentsByDistributor: function(){
            var $form = $("#add-customer-form");

            $form.on('change', '#distributor_id', function(){
                // Get size categories by selected is_grouped value
                $.ajax({
                    type: "POST",
                    url: $('#distributor_id').data('get-agents-url'),
                    async: true,
                    data: {distributor_id: $(this).val() },
                    success: function(response) {
                        // Preparing Dropdown
                        // if(response._data)
                        // {
                        //     $form.find('#agent_id').html(response._data);
                        //     $form.find('#agent_id').selectpicker('refresh');
                        // }
                        $form.find('#agent_id').html(response._data);
                        $form.find('#agent_id').selectpicker('refresh');
                    },
                });
            });
        }
    };
})();

Users.init();