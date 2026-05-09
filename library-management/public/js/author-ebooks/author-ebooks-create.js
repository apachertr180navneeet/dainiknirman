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
            var $form = $('#add-book-form');
            
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
                    minDate: "today",
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
            var $form = $("#add-book-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    book_name: {
                        required: true,
                        remote: {
                            url: $("#book_name").data("check-url"),
                            type: "post",
                            data: {
                                book_name: function() {
                                    return $("#book_name").val();
                                }
                            }
                        }
                    },
                    author_name: {
                        required: true,
                    },
                    launch_date: {
                        required: true,
                    },
                    book_type: {
                        required: true,
                    },
                    cover_picture: {
                        required: true,
                        accept: "image/jpg, image/jpeg, image/png, image/gif"
                    },
                    book_pdf: {
                        required: true,
                        accept: "pdf"
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    book_name: {
                        required: "This field is required."
                    },
                    author_name: {
                        required: "This field is required."
                    },
                    launch_date: {
                        required: "This field is required."
                    },
                    book_type: {
                        required: "This field is required."
                    },
                    cover_picture:{
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    book_pdf: {
                        required: "This field is required.",
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

Books.init();