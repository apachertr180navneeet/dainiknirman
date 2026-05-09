var Services = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Services.validateForm();
            Services.initializeComponents();
            Services.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#edit-service-form');            
            
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
            var $form = $("#edit-service-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: $("#name").data("check-url"),
                            type: "post",
                            data: {
                                name: function() {
                                    return $("#name").val();
                                },
                                service_id: function() {
                                    return $("#service_id").val();
                                },
                            }
                        }
                    },
                    image: {
                        required: {
                            depends: function(){
                                if(($("#image").attr("data-default-file") === undefined || $("#image").attr("data-default-file") == '')){
                                    return true;
                                }
                            }
                        },
                        accept: "image/jpg, image/jpeg, image/png, image/gif"
                    },
                    amount: {
                        required: true,
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    name: {
                        required: "This field is required.",
                        remote: "The service name already exists."
                    },
                    image: {
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    amount: {
                        required: "This field is required.",
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

Services.init();