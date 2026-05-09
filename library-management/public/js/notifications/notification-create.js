var Books = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Books.validateForm();
            Books.initializeComponents();
            Books.customValidationMethods();
            Books.getAuthors();
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
                    'author_id[]': {
                        required: {
                            depends: function(){
                                if($("#category_type").val() != 'NATIVE'){
                                    return true;
                                }
                            }
                        }
                    },
                    original_price: {
                        required: {
                            depends: function(){
                                if($("#book_type").val() == 'P'){
                                    return true;
                                }
                            }
                        },
                        number: true
                    },
                    price: {
                        required: {
                            depends: function(){
                                if($("#book_type").val() == 'P'){
                                    return true;
                                }
                            }
                        },
                        number: true
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    book_name: {
                        required: "This field is required.",
                        remote: "Book name already exists."
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
                    'author_id[]': {
                        required: "This field is required."
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
        getAuthors: function(){
            var $form = $("#add-book-form");
            $form.find("#category_type").trigger("change");

            $form.on('change', '#category_type', function(){
                // Get size categories by selected is_grouped value
                $.ajax({
                    type: "POST",
                    url: $('#category_type').data('get-authors'),
                    async: true,
                    data: {category_type: $(this).val() },
                    success: function(response) {
                        // Preparing Dropdown
                        // if(response._data)
                        // {
                        //     $form.find('#agent_id').html(response._data);
                        //     $form.find('#agent_id').selectpicker('refresh');
                        // }
                        $form.find('#author_id').html(response.data);

                        console.log($("#category_type").val());
                        // $form.find('#author_id').selectpicker('destroy');
                        

                        if($("#category_type").val() == 'ANTHOLOGY'){
                            $("#author_id").attr("name", 'author_id[]');
                            $("#author_id").attr("multiple", true);
                            $("#author_id").removeAttr("disabled");
                            $('#author_id').selectpicker('refresh');
                        }
                        else if($("#category_type").val() == 'SINGLE_AUTHOR'){
                            $("#author_id").attr("name", 'author_id');
                            $("#author_id").removeAttr("multiple");
                            $("#author_id").removeAttr("disabled");
                            $('#author_id').selectpicker('refresh');
                        }
                        else{
                            $("#author_id").attr("name", 'author_id');
                            $("#author_id").attr("disabled", "disabled");
                            $('#author_id').selectpicker('disabled');
                            $('#author_id').selectpicker('refresh');
                        }

                        // $('#author_id').selectpicker('refresh');
                    },
                });
            });
        }
    };
})();

Books.init();