var Components = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            $(window).bind("pageshow", function() {
                $(".form-control-sm").val("");
            });
        },

        /**
         * Date picker.
         */
        datePicker: function($source) {
            var $date_picker = $source.find(".date-picker");

            if ($date_picker.length) {
                $date_picker.datepicker({
                    format: "dd-mm-yyyy",
                });
            }
        },

        /**
         * Image preview.
         */
        imagePreview: function($source) {
            var $image_preview = $source.find(".image-preview");

            if ($image_preview.length) {
                $image_preview.dropify({
                    // allowedFileExtensions: "jpg jpeg png gif svg",
                    showRemove: true,
                    showErrors: true,
                    errorsPosition: 'outside',
                    // Default messages update
                    messages: {
                        'default': 'Drag and drop a file here or click',
                        'replace': 'Drag and drop or click to replace',
                        'remove':  'Remove',
                        'error':   ''
                    },

                    // Default errors messages
                    error: {
                        'fileSize': 'The file size is too big ({{ value }} max).',
                        'minWidth': 'The image width is too small ({{ value }}}px min).',
                        'maxWidth': 'The image width is too big ({{ value }}}px max).',
                        'minHeight': 'The image height is too small ({{ value }}}px min).',
                        'maxHeight': 'The image height is too big ({{ value }}px max).',
                        'imageFormat': 'Only {{ value }} image(s) allowed.',
                        'fileExtension': 'Only {{ value }} file(s) allowed.'
                    },

                    // Templates update
                    tpl: {
                        wrap:            '<div class="dropify-wrapper"></div>',
                        loader:          '<div class="dropify-loader"></div>',
                        message:         '<div class="dropify-message"><span class="file-icon" /> <p>{{ default }}</p></div>',
                        preview:         '<div class="dropify-preview"><span class="dropify-render"></span><div class="dropify-infos"><div class="dropify-infos-inner"><p class="dropify-infos-message">{{ replace }}</p></div></div></div>',
                        filename:        '<p class="dropify-filename"><span class="file-icon"></span> <span class="dropify-filename-inner"></span></p>',
                        clearButton:     '<button type="button" class="dropify-clear">{{ remove }}</button>',
                        errorLine:       '<p class="dropify-error">{{ error }}</p>',
                        errorsContainer: '<div class="dropify-errors-container-custom"><ul></ul></div>'
                    }
                });
            }
        },

        /**
         * Bootstrap select.
         */
        bootstrapSelect: function($source) {
            var $select = $source.find(".select-picker");

            if ($select.length) {
                $select.selectpicker({
                    liveSearch: true,
                    selectedTextFormat: "count > 2",
                    size: "8",
                    iconBase: "fontawesome",
                    tickIcon: "fa fa-check"
                });
            }
        },

        /**
         * Add new entity.
         */
        addNewEntity: function($source) {
            $source.on("click", ".add-new-entity", function() {
                var $this = $(this);
                var $add_new_entity_modal = $("#add-new-entity-modal");

                $add_new_entity_modal.modal("show");
                $add_new_entity_modal
                    .find(".modal-content")
                    .load($this.data("url"), function() {
                        Components.validateAddNewEntityForm(
                            $add_new_entity_modal
                        );
                        Components.addNewEntityForm(
                            $add_new_entity_modal,
                            $this
                        );
                        Components.bootstrapSelect($add_new_entity_modal);
                    });

                $add_new_entity_modal.on("hidden.bs.modal", function() {
                    App.resetModal($add_new_entity_modal);
                });
            });
        },

        /**
         * Validate add new entity form.
         */
        validateAddNewEntityForm: function($source) {
            var $form = $source.find("form");

            $form.find("select").change(function() {
                $(this).valid();
            });

            $form.validate({
                // @validation states + elements
                errorClass: "invalid-feedback",
                errorElement: "span",
                //------------------------------

                // @validation rules
                rules: {
                    //
                },
                //------------------

                // @validation error messages
                messages: {
                    //
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

                submitHandler: function(form) {
                    //
                }
            });
        },

        /**
         * Add new entity form.
         */
        addNewEntityForm: function($source, $this) {
            $source.find("form").on("click", ".btn-submit", function(e) {
                e.preventDefault();

                if ($source.find("form").valid()) {
                    $form = $source.find("form");

                    $.ajax({
                        url: $form.attr("action"),
                        type: "POST",
                        data: $form.serialize(),
                        cache: false,
                        processData: false,
                        beforeSend: function() {
                            $form.find("#custom-error").html("");
                            App.formLoading($form);
                        },
                        success: function(response) {
                            var data = response._data;
                            var grouped = response.grouped;

                            if (response._status == true) {
                                if (grouped) {
                                    var $form_group = $this.closest(
                                        ".form-group"
                                    );
                                    var selectedText = $form
                                        .find("select option:selected")
                                        .text();
                                    var $optgroups = $form_group.find(
                                        "select optgroup"
                                    );
                                    var $addable_optgroup = null;

                                    $.each($optgroups, function(
                                        index,
                                        optgroup
                                    ) {
                                        var $optgroup = $(optgroup);

                                        if (
                                            $optgroup.attr("label") ==
                                            selectedText
                                        ) {
                                            $addable_optgroup = $optgroup;

                                            return false;
                                        }
                                    });

                                    if ($addable_optgroup) {
                                        var html = "";
                                        html +=
                                            '<option selected value="' +
                                            data.id +
                                            '">' +
                                            data.name +
                                            "</option>";
                                        $addable_optgroup.append(html);
                                        $form_group
                                            .find("select")
                                            .selectpicker("refresh");
                                    }
                                } else {
                                    var html = "";
                                    var $form_group = $this.closest(
                                        ".form-group"
                                    );
                                    html +=
                                        '<option selected value="' +
                                        data.id +
                                        '">' +
                                        data.name +
                                        "</option>";
                                    $form_group.find("select").append(html);
                                    $form_group
                                        .find("select")
                                        .selectpicker("refresh");
                                }
                                $source.modal("hide");
                            }

                            // Show notification
                            App.showNotification(response);
                            //------------------
                        },
                        error: function(response) {
                            if (response.responseJSON._status == false) {
                                $form
                                    .find("#custom-error")
                                    .html(
                                        '<div class="alert dark alert-danger alert-dismissible"> ' +
                                            response.responseJSON._message +
                                            " </div>"
                                    );
                            }
                        },
                        complete: function(response) {
                            App.stopFormLoading($form);
                        }
                    });
                    return false;
                }
            });
        },

        /**
         * Description editor.
         */
        descriptionEditor: function ($source) {
            $source.find(".editor-textarea").summernote({
                placeholder: "Enter description here...",
                height: 300,
                toolbar: [
                    ['view', ['codeview', 'undo', 'redo']],
                    ["style", ["bold", "italic", "underline", "clear"]],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['height', ['height']],
                    ['insert', ['link', 'picture', 'video']],
                ],
                callbacks: {
                    onKeyup: function () {
                        $(this).valid();
                    },
                    onFocus: function () {
                        $(this).valid();
                    },
                    onBlur: function () {
                        $(this).valid();
                    },
                },
            });
        },

        /**
         * Remove image.
         */
        removeImage: function($source) {
            var $image = $(".image-main");

            $source.on("click", ".dropify-clear", function() {
                $.ajax({
                    type: "POST",
                    url: $image.data("remove-image-url"),
                    data: {
                        id: $image.data("remove-image-id")
                    },
                    beforeSend: function() {
                        //
                    },
                    success: function(response) {
                        App.showNotification(response);
                    },
                    error: function() {},
                    complete: function() {
                        //
                    }
                });
            });
        },

        /**
         *  Enable Button.
         */
        enableButton: function ($source) {
            $source.find(':input[name="filter"]').prop("disabled", true);
            $source.find(':input[name="clear"]').prop("disabled", true);
            $source.find('a.clear-filter').addClass("disabled");

            $('input[type="text"]').keyup(function () {
                if ($(this).val() != "") {
                    $source.find(':input[name="filter"]').prop("disabled", false);
                    $source.find(':input[name="clear"]').prop("disabled", false);
                    $source.find('a.clear-filter').removeClass("disabled");
                }

                // Checking if all filters are blank
                checkBlankFilter($source);
            });
            $('input[type="text"]').change(function () {
                if ($(this).val() != "") {
                    $source.find(':input[name="filter"]').prop("disabled", false);
                    $source.find(':input[name="clear"]').prop("disabled", false);
                    $source.find('a.clear-filter').removeClass("disabled");
                }

                // Checking if all filters are blank
                checkBlankFilter($source);
            });
            $source.find(".select-picker").change(function () {
                if ($(this).val() != "") {
                    $source.find(':input[name="filter"]').prop("disabled", false);
                    $source.find(':input[name="clear"]').prop("disabled", false);
                    $source.find('a.clear-filter').removeClass("disabled");
                }

                // Checking if all filters are blank
                checkBlankFilter($source);
            });
            $source.find(".date-picker").click(function () {
                if ($(this).val() != "") {
                    $source.find(':input[name="filter"]').prop("disabled", false);
                    $source.find(':input[name="clear"]').prop("disabled", false);
                    $source.find('a.clear-filter').removeClass("disabled");
                }

                // Checking if all filters are blank
                checkBlankFilter($source);
            });
            $source.find(".date-picker").change(function () {
                if ($(this).val() != "") {
                    $source.find(':input[name="filter"]').prop("disabled", false);
                    $source.find(':input[name="clear"]').prop("disabled", false);
                    $source.find('a.clear-filter').removeClass("disabled");
                }

                // Checking if all filters are blank
                checkBlankFilter($source);
            });
        },

        /**
         * Bootstrap tags input.
         */
        tagsInput: function($source) {
            var $tagInput = $source.find(".tags-input");

            if ($tagInput.length) {
                $tagInput.tagsinput({
                    trimValue: true,
                    allowDuplicates: false,
                    confirmKeys: [13, 44],
                    cancelConfirmKeysOnEmpty: false,
                });

                $tagInput.tagsinput('refresh');

                $tagInput.on('beforeItemAdd', function(event) {
                    // event.item: contains the item
                    // event.cancel: set to true to prevent the item getting added
                    
                    if(!/^[0-9a-zA-z]{5,6}$/.test(event.item)){
                        event.cancel = true;
                    }
                });
            }
        },

        /**
         * Components Custom validation methods.
         */
        additionalValidationMethods: function() {

            jQuery.validator.addMethod(
                "imageSizeCheck",
                function(value, element) {
                    if($(element)[0].files.length > 0){
                        var imageSize = $(element)[0].files[0].size / (1024 * 1024);
                        if (imageSize > maxImageSize) {
                            return false;
                        } else {
                            return true;
                        }
                    }
                    else {
                        return true;
                    }
                },
                "Please upload image up to 3MB"
            );
        },
    };
})();

Components.init();

function checkBlankFilter($source)
{
    var isBlank = 0;
    var totalFields = $source.find('.filter-field').length;

    $source.find('.filter-field').each(function(){
        if($(this).val() == '')
        {
            isBlank += 1;
        }
    });
    if(isBlank > 0 && totalFields == isBlank)
    {
        $source.find(':input[name="filter"]').prop("disabled", true);
        $source.find(':input[name="clear"]').prop("disabled", true);
        $source.find('a.clear-filter').addClass("disabled");
    }
    else 
    {
        $source.find(':input[name="filter"]').prop("disabled", false);
        $source.find(':input[name="clear"]').prop("disabled", false);
        $source.find('a.clear-filter').removeClass("disabled");
    }
}