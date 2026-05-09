var App = (function() {
    return {
        /**
         * Initialization.
         */
        init: function() {
            App.setupAjax();
            App.markNotificationAsRead();
        },

        swalToast: function(type, title){
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: type,
                title: title
            });
        },

        /**
         * Setup AJAX.
         */
        setupAjax: function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
        },

        /**
         * Show notification.
         *
         * @param {*} notification
         */
        showNotification: function(notification) {
            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: notification._type,
                title: notification._message
            });
        },

        /**
         * Form loading.
         */
        formLoading: function($form) {
            var $btn_submit = $form.find(".btn-submit");

            $btn_submit
                .find(".fa")
                .removeClass("fa-save")
                .addClass("fa-refresh fa-spin");
            $btn_submit.prop("disabled", true);
        },

        /**
         * Stop form loading.
         */
        stopFormLoading: function($form) {
            var $btn_submit = $form.find(".btn-submit");

            $btn_submit
                .find(".fa")
                .removeClass("fa-refresh fa-spin")
                .addClass("fa-save");
            $btn_submit.prop("disabled", false);
        },

        /**
         * Reset modal.
         */
        resetModal: function($modal) {
            var html = '<div class="modal-loading"> Loading.... </div>';
            $modal.find(".modal-content").html(html);
        },

        /**
         * Array Column.
         */
        arrayColumn: function(array, columnName) {
            return array.map(function(value, index) {
                return value[columnName];
            });
        },

        /**
         * Mark notification as read.
         */
        markNotificationAsRead: function() {
            $source = $(".notification-dropdown");
            $source.on("click", ".notification-icon", function() {
                var $this = $(this);
                $.ajax({
                    type: "GET",
                    url: $this.data("url"),
                    success: function(response) {
                        console.log(response);
                        $(".notification-count-div").html("");
                    }
                });
            });
        },

        /**
         * Filter Form loading.
         */
        filterFormLoading: function($form) {
            var $btn_submit = $form.find(".apply-filter");
            var $btn_clear_filters = $form.find(".clear-filter");
            $btn_submit.prepend('<i class="fa fa-refresh fa-spin"></i> ');
            $btn_submit.prop("disabled", true);
            $btn_clear_filters.prop("disabled", true);
            $form.find("input, select").prop('disabled', true);
        },

        /**
         * Stop form loading.
         */
        stopFilterFormLoading: function($form) {
            var $btn_submit = $form.find(".apply-filter");
            var $btn_clear_filters = $form.find(".clear-filter");
            $btn_submit.find("i").remove();
            $btn_submit.prop("disabled", false);
            $btn_clear_filters.prop("disabled", false);
            $form.find("input, select").prop('disabled', false);
        },
    };
})();

App.init();
