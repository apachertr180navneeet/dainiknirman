var ContestAuthors = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            ContestAuthors.validateForm();
            ContestAuthors.initializeComponents();
            ContestAuthors.customValidationMethods();
            // ContestAuthors.validateRankSelection();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#edit-contest-author-form');
            
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
                var minDate = "";
                if($("#date").val() == ""){
                    minDate = "today"
                }
                $date_range_picker.flatpickr({
                    dateFormat: "d-m-Y",
                    // mode: "range",
                    // maxDate: "today",
                    minDate: minDate
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
            var $form = $("#edit-contest-author-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    rank: {
                        required: true,
                        remote: {
                            url: $("#rank").data("check-url"),
                            type: "post",
                            data: {
                                rank: function() {
                                    return $("#rank").val();
                                },
                                contest_id: function() {
                                    return $("#contest_id").val();
                                },
                                contest_author_id: function() {
                                    return $("#contest_author_id").val();
                                },
                            }
                        }
                    },
                    admin_remark: {
                        required: false
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    rank: {
                        required: "This field is required.",
                        remote: "The rank already given."
                    },
                    admin_remark: {
                        required: "This field is required."
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
        validateRankSelection: function(){
            var $form = $("#edit-contest-author-form");
            $form.on('change', '#rank', function(){
                $(this).valid();
            });

            // $form.on('change', '#distributor_id', function(){
            //     // Get size categories by selected is_grouped value
            //     $.ajax({
            //         type: "POST",
            //         url: $('#distributor_id').data('get-agents-url'),
            //         async: true,
            //         data: {
            //             distributor_id: $('#distributor_id').val(),
            //             agent_id: $('#customer_agent_id').val(),
            //         },
            //         success: function(response) {
            //             // Preparing Dropdown
            //             // if(response._data)
            //             // {
            //             //     $form.find('#agent_id').html(response._data);
            //             //     $form.find('#agent_id').selectpicker('refresh');
            //             // }
            //             $form.find('#agent_id').html(response._data);
            //             $form.find('#agent_id').selectpicker('refresh');
            //         },
            //     });
            // });
        },
    };
})();

ContestAuthors.init();