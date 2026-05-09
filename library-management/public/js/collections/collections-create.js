var Categories = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Categories.validateForm();
            Categories.showElementByIsGrouped();
            Categories.showSizeCategory();
            Categories.initializeComponents();
            Categories.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#add-collection-form');
            
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
            var $form = $("#add-collection-form");
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
                                }
                            }
                        }
                    },
                    'product_id[]': {
                        required: true
                    },
                    image: {
                        required: false,
                        accept: "image/jpg, image/jpeg, image/png, image/gif",
                    },
                    status: {
                        required: true,
                    }
                },
                //------------------

                // @validation error messages
                messages: {
                    name: {
                        required: "This field is required.",
                        remote: "The category name already exists."
                    },
                    'product_id[]': {
                        required: "This field is required.",
                    },
                    image:{
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
                    },
                    status: {
                        required: "This field is required.",
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

        showElementByIsGrouped: function(){
            var $form = $(".add-category-form");
            $form.on('change', 'input[name="is_grouped"]', function(){
                var isGrouped = $(this).val();

                $("#category_group").val('');
                $("#size_category").val('');
                
                if($(this).val() == 1){
                    $("#category_group").prop('disabled', '');
                    $("#category_group").selectpicker('refresh');

                    $("#size_category").selectpicker({
                        maxOptions: 1,
                        size: 10
                    });
                }
                else{
                    $("#category_group").prop('disabled', 'disabled');
                    $("#category_group").selectpicker('refresh');

                    $("#size_category").selectpicker({
                        maxOptions: false,
                        size: 10
                    });
                }

                $("#size_category").selectpicker('refresh');

                // Validate
                $("#category_group").valid();
                
                // Get size categories by selected is_grouped value
                $.ajax({
                    type: "POST",
                    url: $('.is-grouped-container').data('get-size-categories-url'),
                    async: false,
                    data: {is_grouped: isGrouped },
                    success: function(response) {
                        var options = '';

                        // Preparing Dropdown
                        if(response._data)
                        {
                            // Main Data
                            $.each(response._data, function(index,data){
                                // Get Design Code
                                var sizeCategoryId = data.id;
                                var sizeCategoryName = data.name;
                                options += '<option value="'+sizeCategoryId+'">'+sizeCategoryName+'</option>';
                            });
                        }

                        $form.find('#size_category').html(options);
                        $form.find('#size_category').selectpicker('refresh');
                    },
                });
            });
        },

        showSizeCategory: function(){
            var $form = $(".add-category-form");
            $form.on('change', '#parent_id', function(){
                var isGrouped = $("input[name='is_grouped']:checked").val();

                $("#category_group").val('');
                $("#size_category").val('');

                if($(this).val() == ''){
                    $("input[name='is_grouped']").prop('disabled', '');

                    $("#size_category").prop('disabled', '');
                    $("#size_category").selectpicker('refresh');
                }
                else{
                    $("input[name='is_grouped'][value='0']").prop('checked', true);
                    $("input[name='is_grouped']").prop('disabled', 'disabled');
                    
                    $("#category_group").attr('disabled', 'disabled');
                    $("#category_group").selectpicker('refresh');

                    $("#size_category").prop('disabled', 'disabled');
                    $("#size_category").selectpicker('refresh');
                }

                // Is grouped conditions
                if($("input[name='is_grouped']:checked").val() == 1){
                    $("#size_category").selectpicker({
                        maxOptions: 1,
                        size: 10
                    });
                }
                else{
                    $("#size_category").selectpicker({
                        maxOptions: false,
                        size: 10
                    });
                }

                $("#size_category").selectpicker('refresh');

                // Get size categories by selected is_grouped value
                $.ajax({
                    type: "POST",
                    url: $('.is-grouped-container').data('get-size-categories-url'),
                    async: false,
                    data: {is_grouped: isGrouped },
                    success: function(response) {
                        var options = '';

                        // Preparing Dropdown
                        if(response._data)
                        {
                            // Main Data
                            $.each(response._data, function(index,data){
                                // Get Design Code
                                var sizeCategoryId = data.id;
                                var sizeCategoryName = data.name;
                                options += '<option value="'+sizeCategoryId+'">'+sizeCategoryName+'</option>';
                            });
                        }

                        $form.find('#size_category').html(options);
                        $form.find('#size_category').selectpicker('refresh');
                    },
                });
                // Is grouped conditions end

                // Validate
                $("#category_group").valid();
                $("#size_category").valid();

                // Remove sequence block for non-root categories
                var colClassMd_6 = 'col-md-6';
                var colClassLg_6 = 'col-lg-6';
                var colClassXl_6 = 'col-xl-6';

                var colClassMd_12 = 'col-md-12';
                var colClassLg_12 = 'col-lg-12';
                var colClassXl_12 = 'col-xl-12';
                
                if($(this).children("option:selected").val() != '' || $(this).children("option:selected").data("root-category") == 'N'){
                    $(".cat_filters_order").hide();

                    $(".cat_order_status").addClass(colClassMd_12);
                    $(".cat_order_status").addClass(colClassLg_12);
                    $(".cat_order_status").addClass(colClassXl_12);
                    $(".cat_order_status").css('display', 'contents');

                    $(".cat_order_status").removeClass(colClassMd_6);
                    $(".cat_order_status").removeClass(colClassLg_6);
                    $(".cat_order_status").removeClass(colClassXl_6);

                    $(".cat_order").addClass(colClassMd_6);
                    $(".cat_order").addClass(colClassLg_6);
                    $(".cat_order").addClass(colClassXl_6);

                    $(".cat_order").removeClass(colClassMd_12);
                    $(".cat_order").removeClass(colClassLg_12);
                    $(".cat_order").removeClass(colClassXl_12);

                    $(".cat_status").addClass(colClassMd_6);
                    $(".cat_status").addClass(colClassLg_6);
                    $(".cat_status").addClass(colClassXl_6);

                    $(".cat_status").removeClass(colClassMd_12);
                    $(".cat_status").removeClass(colClassLg_12);
                    $(".cat_status").removeClass(colClassXl_12);
                }
                else{
                    $(".cat_filters_order").show();

                    $(".cat_order_status").removeClass(colClassMd_12);
                    $(".cat_order_status").removeClass(colClassLg_12);
                    $(".cat_order_status").removeClass(colClassXl_12);
                    $(".cat_order_status").removeAttr('style');

                    $(".cat_order_status").addClass(colClassMd_6);
                    $(".cat_order_status").addClass(colClassLg_6);
                    $(".cat_order_status").addClass(colClassXl_6);

                    $(".cat_order").removeClass(colClassMd_6);
                    $(".cat_order").removeClass(colClassLg_6);
                    $(".cat_order").removeClass(colClassXl_6);

                    $(".cat_order").addClass(colClassMd_12);
                    $(".cat_order").addClass(colClassLg_12);
                    $(".cat_order").addClass(colClassXl_12);

                    $(".cat_status").removeClass(colClassMd_6);
                    $(".cat_status").removeClass(colClassLg_6);
                    $(".cat_status").removeClass(colClassXl_6);

                    $(".cat_status").addClass(colClassMd_12);
                    $(".cat_status").addClass(colClassLg_12);
                    $(".cat_status").addClass(colClassXl_12);
                }
            });
        }
    };
})();

Categories.init();