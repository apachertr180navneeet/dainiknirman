var Magazines = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Magazines.validateForm();
            Magazines.initializeComponents();
            Magazines.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#edit-magazine-form');
            
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
            var $form = $("#edit-magazine-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    title: {
                        required: true,
                        remote: {
                            url: $("#title").data("check-url"),
                            type: "post",
                            data: {
                                title: function() {
                                    return $("#title").val();
                                },
                                magazine_id: function() {
                                    return $("#magazine_id").val();
                                },
                            }
                        }
                    },
                    date: {
                        required: true,
                    },
                    type: {
                        required: true,
                    },
                    cover_picture: {
                        required: {
                            depends: function(){
                                if(($("#cover_picture").attr("data-default-file") === undefined || $("#cover_picture").attr("data-default-file") == '')){
                                    return true;
                                }
                            }
                        },
                        maxFileSize: {
                            unit: "MB",
                            size: 10
                        },
                        accept: "image/jpg, image/jpeg, image/png, image/gif"
                    },
                    magazine_pdf: {
                        required: {
                            depends: function(){
                                if(($("#magazine_pdf_name").val() === undefined || $("#magazine_pdf_name").val() == '')){
                                    return true;
                                }
                            }
                        },
                        maxFileSize: {
                            unit: "MB",
                            size: 10
                        },
                        accept: "pdf"
                    }
                },
                //------------------

                // @validation error messages
                messages: {
                    title: {
                        required: "This field is required.",
                        remote: "The title is already exists"
                    },
                    date: {
                        required: "This field is required."
                    },
                    type: {
                        required: "This field is required."
                    },
                    cover_picture:{
                        required: "This field is required.",
                        maxFileSize: "Cover picture cannot be more than 10MB.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    magazine_pdf: {
                        required: "This field is required.",
                        // maxFileSize: "PDF cannot be more than 1MB.",
                        accept: "Only PDF files are allowed."
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

Magazines.init();