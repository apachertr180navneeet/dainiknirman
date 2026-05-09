var Categories = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            Categories.validateForm();
            Categories.toggleCatalogType();
            Categories.toggleProductTagsFormat();
            Categories.initializeComponents();
            Categories.customValidationMethods();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            var $form = $('#add-catalog-form');
            
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
            
            jQuery.validator.addMethod(
                "lessThanToPrice",
                function(value, element) {
                    if(this.optional(element) || (value > 0 && value <= parseFloat($("#to_price").val())))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                },
                "From price must be less than 'To Price'."
            );

            jQuery.validator.addMethod(
                "greaterThanFromPrice",
                function(value, element) {
                    if(this.optional(element) || (value > 0 && value >= parseFloat($("#from_price").val())))
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                },
                "To price must be greater than 'From Price'."
            );
        },

        /**
         * Validate country form.
         */
        validateForm: function() {
            var $form = $("#add-catalog-form");
            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    'category_id[]': {
                        required: {
                            depends: function(){
                                return ($("input[name='catalog_type']:checked").val() == 'CATEGORY');
                            }
                        }
                    },
                    'collection_id[]': {
                        required: {
                            depends: function(){
                                return ($("input[name='catalog_type']:checked").val() == 'COLLECTION');
                            }
                        }
                    },
                    from_price: {
                        required: {
                            depends: function(){
                                return ($("#to_price").val() > 0);
                            }
                        },
                        // lessThanToPrice : {
                        //     depends: function(){
                        //         return ($("#from_price").val() > $("#to_price").val());
                        //     }
                        // }
                        lessThanToPrice: true
                    },
                    to_price: {
                        required: {
                            depends: function(){
                                return ($("#from_price").val() > 0);
                            }
                        },
                        // greaterThanFromPrice: {
                        //     depends: function(){
                        //         return ($("#to_price").val() < $("#from_price").val());
                        //     }
                        // }
                        greaterThanFromPrice : true
                    },
                    image: {
                        required: false,
                        accept: "image/jpg, image/jpeg, image/png, image/gif",
                    },
                },
                //------------------

                // @validation error messages
                messages: {
                    from_price: {
                        required: "This field is required.",
                    },
                    to_price: {
                        required: "This field is required.",
                        remote: "The category name already exists."
                    },
                    image:{
                        required: "This field is required.",
                        accept: "Only JPG, PNG and GIF files are allowed."
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
        },

        toggleCatalogType: function(){
            var $form = $("#add-catalog-form");

            // Default selection update
            // $form.find("#collection_id").removeAttr("required");
            // $form.find("#collection_id").removeClass("select-picker");
            // $form.find("#category_id").val("");
            // $form.find("#collection_id").val("");

            $form.on('change', 'input[name="catalog_type"]', function(){
                var catalogType = $(this).val();

                if(catalogType == 'CATEGORY')
                {
                    $form.find("#collection_id").removeAttr("required");
                    $form.find("#collection_id").removeClass("select-picker");
                    $form.find("#collection_id").val("");
                    $(".collection-container").addClass("d-none");

                    $(".category-container").removeClass("d-none");
                    $form.find("#category_id").attr("required");
                    $form.find("#category_id").addClass("select-picker");
                    $form.find("#category_id").val("");
                    $form.find("#category_id").selectpicker("refresh");
                }
                else if(catalogType == 'COLLECTION')
                {
                    $form.find("#category_id").removeAttr("required");
                    $form.find("#category_id").removeClass("select-picker");
                    $form.find("#category_id").val("");
                    $(".category-container").addClass("d-none");

                    $(".collection-container").removeClass("d-none");
                    $form.find("#collection_id").attr("required");
                    $form.find("#collection_id").addClass("select-picker");
                    $form.find("#collection_id").val("");
                    $form.find("#collection_id").selectpicker("refresh");
                }
                else
                {
                    //
                }
            });
        },

        toggleProductTagsFormat: function(){
            var $form = $("#add-catalog-form");

            $form.on('change', 'select[name="type"]', function(){
                var catalogType = $(this).val();

                if(catalogType == 'f3')
                {
                    $form.find("#product_tags_format").removeClass("d-none");
                }
                else
                {
                    $form.find("input[name='product_tags'][value='ALL']").prop("checked", true);
                    $form.find("#product_tags_format").addClass("d-none");
                }
            });
        }
    };
})();

Categories.init();