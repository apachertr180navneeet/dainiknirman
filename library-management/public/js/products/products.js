var Products = (function() {

    // Array holding selected row IDs
    var rows_selected = [];
    var data_table;
    
    return {
        /**
         * Initialization.
         */
        init: function() {
            Products.getRecords();
            Products.changeStatus();
            Products.destroyRecords();
            Products.deleteRecord();
            Products.updateOrder();
            Products.checkTags();
            Products.removeTags();
            Products.dataTableCustomFilter();
            Products.initializeComponents();
        },

        /**
         * Initialize components.
         */
        initializeComponents: function() {
            // Initialize Components
            var $filter_form = $(".custom-datatable-filter-form");

            // Bootstrap Select on filter form dropdowns
            Components.bootstrapSelect($filter_form);
            //------------

            // Enable Button on change filter form elements
            Components.enableButton($filter_form);
            //------------
        },

        /**
         * Datatable custom filter.
         */
        dataTableCustomFilter: function() {
            $(".filter-button").click(function() {
                $(".custom-datatable-filters").toggleClass("d-none");
            });
        },

        /**
         * Updates "Select all" control in a data table
         */
        updateDataTableSelectAllCtrl: function(table) {
            var $table = table.table().node();
            var $chkbox_all = $('tbody input[type="checkbox"]', $table);
            var $chkbox_checked = $(
                'tbody input[type="checkbox"]:checked',
                $table
            );
            var chkbox_select_all = $(
                'thead input[name="select_all"]',
                $table
            ).get(0);

            // If none of the checkboxes are checked
            if ($chkbox_checked.length === 0) {
                chkbox_select_all.checked = false;

                if ("indeterminate" in chkbox_select_all) {
                    chkbox_select_all.indeterminate = false;
                }

                // If all of the checkboxes are checked
            } else if ($chkbox_checked.length === $chkbox_all.length) {
                chkbox_select_all.checked = true;

                if ("indeterminate" in chkbox_select_all) {
                    chkbox_select_all.indeterminate = false;
                }

                // If some of the checkboxes are checked
            } else {
                chkbox_select_all.checked = true;
                if ("indeterminate" in chkbox_select_all) {
                    chkbox_select_all.indeterminate = true;
                }
            }
        },

        /**
         * Get Records list.
         */
        getRecords: function() {
            var $dataTable = $("#dataTable");

            data_table = table = $dataTable.DataTable({
                initComplete: function() {
                    // Show default first value selected
                    data_table.page.len('10');
                    
                    
                        $(".dt-buttons").addClass("btn-toolbar").removeClass("btn-group");
                        $(".current-page-button").addClass(
                            "btn btn-icon _btn-rounded btn-primary btn-outline"
                        );
                        $(".current-page-button").attr(
                            "title",
                            "Export Current Page"
                        );
                        $(".current-page-button").html(
                            '<i title="Export Excel" class="fa fa-file-text"/> &nbsp; Export Current Page'
                        );
    
                        $(".all-page-button").addClass(
                            "btn btn-icon _btn-rounded btn-primary btn-outline"
                        );
                        $(".all-page-button").attr("title", "Export All");
                        $(".all-page-button").html(
                            '<i title="Export Excel" class="fa fa-file-text"/> &nbsp; Export All'
                        );

                        $('.btn-toolbar').append(
                            '<a href="'+$dataTable.data('import-csv-url')+'" title="Import CSV" class="btn btn-icon _btn-rounded btn-primary btn-outline import-csv-all mr-1"> <i class="fa fa-file-import" aria-hidden="true"></i></button> '+
                            '<a href="'+$dataTable.data('export-csv-url')+'" title="Export CSV" class="btn btn-icon _btn-rounded btn-primary btn-outline export-csv-all mr-1"> <i class="fa fa-file-excel" aria-hidden="true" download></i></button> '
                        );

                        $('.btn-toolbar').append(
                            // '<button type="button" title="Update Order" class="btn btn-icon _btn-rounded btn-primary btn-outline update-order"> <i class="fa fa-sort" aria-hidden="true"></i></button> '+
                            '<button type="button" title="Change Status" class="btn btn-icon _btn-rounded btn-primary btn-outline change-status" disabled> <i class="fas fa-exchange-alt" aria-hidden="true"></i></button>&nbsp;'
                        );

                        $('.btn-toolbar').append(
                            '<button type="button" title="Delete" class="btn btn-icon _btn-rounded btn-primary btn-outline dt-delete" disabled> <i class="fa fa-trash" aria-hidden="true"></i></button> '
                        );

                        if (data_table.row().count() == 0) {
                            data_table.buttons(".buttons-excel").nodes().css("display", "none");
                            $(".btn-toolbar .export-csv-all").addClass("disabled");
                            $(".btn-toolbar .update-order").attr("disabled", "disabled");
                        }
                        else {
                            data_table.buttons(".buttons-excel").nodes().css("display", "block");
                            $(".btn-toolbar .export-csv-all").removeClass("disabled");
                            $(".btn-toolbar .update-order").removeAttr("disabled");
                        }
                },
                headerCallback: function(e, a, t, n, s) {
                    e.getElementsByTagName("th")[0].innerHTML =
                            '<label class="new-control new-checkbox checkbox-outline-primary m-auto ckbox">\n<input type="checkbox" name="select_all" class="new-control-input chk-parent select-customers-primary" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>';
                },
                columnDefs: [
                    {
                        targets: 0,
                        width: "30px",
                        className: "",
                        orderable: !1,
                        visible: true,
                        render: function(e, a, t, n) {
                            return '<label class="new-control new-checkbox checkbox-outline-primary  m-auto ckbox">\n<input type="checkbox" class="new-control-input child-chk select-customers-primary" id="customer-all-info">\n<span class="new-control-indicator"></span><span style="visibility:hidden">c</span>\n</label>';
                        }
                    }
                ],
                buttons: {
                    buttons: []
                },
                oLanguage: {
                    sInfo: "Showing records _START_ to _END_ of _TOTAL_",
                    sSearch: '',
                    sSearchPlaceholder: "Search...",
                    sLengthMenu: "Show :  _MENU_"
                },
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 20, 50, 75, 100],
                    [10, 20, 50, 75, 100]
                ],
                pageLength: 10,
                dom: '<"row custom-dom-col"<"col-12 col-md-2 col-lg-1"l> <"col-12 col-lg-3 col-md-3 mr-lg-auto"f> <"col-12 col-lg-8 col-md-7"B> > <"row"<"col-md-12 custom-overflow"rt> > <"row"<"col-md-5"i><"col-md-7"p>>',
                ajax: {
                    url: $dataTable.data("url"),
                    data: function(d) {
                        d.filter_category = $("#parent_category_filter").val();
                        d.filter_tags = $("#remove_tags_name").val();
                    }
                },
                columns: [
                    {
                        data: null,
                        name: "",
                        searchable: false,
                        sortable: false
                    },
                    { data: "image", name: "image", sortable: false, class:"text-center" },
                    { data: "type", name: "type", sortable: false },
                    { data: "name", name: "name", sortable: true },
                    { data: "category_name", name: "category_name", sortable: true },
                    { data: "price", name: "price", sortable: true },
                    { data: "tag_name", name: "tag_name", sortable: true },
                    { data: "status", name: "status", sortable: true },
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                        sortable: false
                    }
                ],
                rowCallback: function(row, data, dataIndex) {
                    // Get row ID
                    var rowId = data[0];

                    // If row ID is in the list of selected row IDs
                    if ($.inArray(rowId, rows_selected) !== -1) {
                        $(row)
                            .find('input[type="checkbox"]')
                            .prop("checked", true);
                        $(row).addClass("selected");
                    }
                },

                drawCallback: function(settings){
                    $source = $(".custom-datatable-filter-form");
                    if(settings.iDraw > 1)
                    {
                        App.stopFilterFormLoading($source);
                    }
                },
            });

            // Apply filter
            $(".apply-filter").on("click", function(e) {
                $source = $(".custom-datatable-filter-form");
                data_table.ajax.reload();
                App.filterFormLoading($source);
                e.preventDefault();
            });
            //-------------

            // Clear filter
            $(".clear-filter").on("click", function(e) {
                $(".custom-datatable-filter-form")[0].reset();
                $source = $(".custom-datatable-filter-form");
                $select = $source.find(".select-picker");
                $select.selectpicker("refresh");

                // Making draw count to 0 to make filters at init state.
                data_table.settings()[0].iDraw = 0;
                data_table.ajax.reload();

                // Disable button click on clear filters
                Components.enableButton($source);
                
                e.preventDefault();
            });
            //-------------

            // Handle click on checkbox
            $dataTable
                .find("tbody")
                .on("click", 'input[type="checkbox"]', function(e) {
                    var $row = $(this).closest("tr");
                    // Get row data
                    var data = table.row($row).data();

                    // Get row ID
                    var rowId = data;

                    // Determine whether row ID is in the list of selected row IDs
                    var index = $.inArray(rowId, rows_selected);

                    // If checkbox is checked and row ID is not in list of selected row IDs
                    if (this.checked && index === -1) {
                        rows_selected.push(rowId);

                        // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                    } else if (!this.checked && index !== -1) {
                        rows_selected.splice(index, 1);
                    }

                    if (
                        $dataTable.find('tbody input[type="checkbox"]:checked')
                            .length > 0
                    ) {
                        $(".change-status").prop("disabled", false);
                        $(".dt-delete").prop("disabled", false);
                    } else {
                        $(".change-status").prop("disabled", true);
                        $(".dt-delete").prop("disabled", true);
                    }

                    if (this.checked) {
                        $row.addClass("selected");
                    } else {
                        $row.removeClass("selected");
                    }

                    // Update state of "Select all" control
                    Products.updateDataTableSelectAllCtrl(table);

                    // Prevent click event from propagating to parent
                    e.stopPropagation();
                });

            // Handle click on "Select all" control
            $dataTable
                .find("thead")
                .on("click", 'input[name="select_all"]', function(e) {
                    if (this.checked) {
                        $dataTable
                            .find('tbody input[type="checkbox"]:not(:checked)')
                            .trigger("click");
                        $(".change-status").prop("disabled", false);
                        $(".dt-delete").prop("disabled", false);
                    } else {
                        $dataTable
                            .find('tbody input[type="checkbox"]:checked')
                            .trigger("click");
                        $(".change-status").prop("disabled", true);
                        $(".dt-delete").prop("disabled", true);
                    }

                    // Prevent click event from propagating to parent
                    e.stopPropagation();
                });

            // Handle table draw event
            table.on("draw", function() {
                // Update state of "Select all" control
                Products.updateDataTableSelectAllCtrl(table);
            });
        },

        /**
         * Change status.
         */
        changeStatus: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");
            var $dataTableWrapper = $(".dataTables_wrapper");

            // Handle form submission event
            $dataTableWrapper.on("click", ".change-status", function() {
                // Iterate over all selected checkboxes
                var ids = [];
                $.each(rows_selected, function(index, rowId) {
                    ids.push(rowId.id);
                });

                $.ajax({
                    type: "POST",
                    url: $dataTable.data("change-status-url"),
                    data: { ids: ids },
                    beforeSend: function() {
                        $(".change-status").prop("disabled", true);
                        $(".dt-delete").prop("disabled", true);
                    },
                    success: function(response) {
                        data_table.ajax.reload(null, false);
                        rows_selected = [];
                        App.showNotification(response);
                    },
                    error: function() {},
                    complete: function() {
                        $(".change-status").prop("disabled", true);
                        $(".dt-delete").prop("disabled", true);
                    }
                });
            });
        },

        /**
         * Destroy record.
         */
        destroyRecords: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");
            var $dataTableWrapper = $(".dataTables_wrapper");

            // Handle form submission event
            $dataTableWrapper.on("click", ".dt-delete", function() {
                // Iterate over all selected checkboxes
                var ids = [];
                $.each(rows_selected, function(index, rowId) {
                    ids.push(rowId.id);
                });

                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                  }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: $dataTable.data("destroy-url"),
                            data: { ids: ids },
                            beforeSend: function() {
                                $(".dt-delete").prop("disabled", true);
                                $(".change-status").prop("disabled", true);
                            },
                            success: function(response) {
                                // Check if rows not available
                                $.each(rows_selected, function(index, rowId) {
                                    data_table.row().remove().draw();
                                });

                                data_table.ajax.reload(null, false);
                                rows_selected = [];

                                // After rows remove draw again and if data length is 0 then disable buttons
                                if (data_table.data().length == 0) {
                                    data_table.buttons(".buttons-excel").nodes().css("display", "none");
                                    $(".btn-toolbar .export-csv-all").addClass("disabled");
                                    $(".btn-toolbar .update-order").attr("disabled", "disabled");
                                }
                                else {
                                    data_table.buttons(".buttons-excel").nodes().css("display", "block");
                                    $(".btn-toolbar .export-csv-all").removeClass("disabled");
                                    $(".btn-toolbar .update-order").removeAttr("disabled");
                                }

                                App.showNotification(response);
                            },
                            error: function() {},
                            complete: function() {
                                $(".dt-delete").prop("disabled", true);
                                $(".change-status").prop("disabled", true);
                            }
                        });

                        // Swal.close()
                    }
                });

                /* iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    color: "yellow",
                    id: "question",
                    zindex: 99999,
                    title: "Hey!",
                    message: "Are you sure to want to delete?",
                    position: "center",
                    progressBar: false,
                    buttons: [
                        [
                            "<button><b>YES</b></button>",
                            function(instance, toast) {

                                $.ajax({
                                    type: "DELETE",
                                    url: $dataTable.data("destroy-url"),
                                    data: { ids: ids },
                                    beforeSend: function() {
                                        $(".dt-delete").prop("disabled", true);
                                        $(".change-status").prop("disabled", true);
                                    },
                                    success: function(response) {
                                        App.showNotification(response);

                                        // Check if rows not available
                                        $.each(rows_selected, function(index, rowId) {
                                            data_table.row().remove().draw();
                                        });

                                        data_table.ajax.reload(null, false);
                                        rows_selected = [];

                                        // After rows remove draw again and if data length is 0 then disable buttons
                                        if (data_table.data().length == 0) {
                                            data_table.buttons(".buttons-excel").nodes().css("display", "none");
                                            $(".btn-toolbar .export-csv-all").addClass("disabled");
                                            $(".btn-toolbar .update-order").attr("disabled", "disabled");
                                        }
                                        else {
                                            data_table.buttons(".buttons-excel").nodes().css("display", "block");
                                            $(".btn-toolbar .export-csv-all").removeClass("disabled");
                                            $(".btn-toolbar .update-order").removeAttr("disabled");
                                        }
                                    },
                                    error: function() {},
                                    complete: function() {
                                        $(".dt-delete").prop("disabled", true);
                                        $(".change-status").prop("disabled", true);
                                    }
                                });
                                instance.hide(
                                    { transitionOut: "fadeOut" },
                                    toast,
                                    "button"
                                );
                            },
                            true
                        ],
                        [
                            "<button>NO</button>",
                            function(instance, toast) {
                                instance.hide(
                                    { transitionOut: "fadeOut" },
                                    toast,
                                    "button"
                                );
                            }
                        ]
                    ],
                    onClosing: function(instance, toast, closedBy) {
                        console.info("Closing | closedBy: " + closedBy);
                    },
                    onClosed: function(instance, toast, closedBy) {
                        console.info("Closed | closedBy: " + closedBy);
                    }
                }); */
            });
        },

        /**
         * Delete Single record.
         */
        deleteRecord: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");

            // Handle form submission event
            $dataTable.on("click", ".dt-delete-single", function(element) {

                
                
                var deleteUrl = $(this).attr('data-url');
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                  }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
                /* iziToast.question({
                    timeout: 20000,
                    close: false,
                    overlay: true,
                    displayMode: "once",
                    color: "yellow",
                    id: "question",
                    zindex: 99999,
                    title: "Hey!",
                    message: "Are you sure to want to delete?",
                    position: "center",
                    progressBar: false,
                    buttons: [
                        [
                            "<button><b>YES</b></button>",
                            function(instance, toast) {
                                window.location.href = deleteUrl;
                            },
                            false
                        ],
                        [
                            "<button>NO</button>",
                            function(instance, toast) {
                                instance.hide(
                                    { transitionOut: "fadeOut" },
                                    toast,
                                    "button"
                                );
                            }
                        ]
                    ],
                    onClosing: function(instance, toast, closedBy) {
                        // console.info("Closing | closedBy: " + closedBy);
                    },
                    onClosed: function(instance, toast, closedBy) {
                        // console.info("Closed | closedBy: " + closedBy);
                    }
                }); */
            });
        },

        /**
         * Update Order
         */
        updateOrder: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");

            // Handle form submission event
            $data_table_container.on("click", ".update-order", function() {
                // Iterate over all selected checkboxes
                var ids = [];
                $(".dataTable tbody tr").each(function(index, rowId) {
                    var orderArr = [];
                    var $row = $(this).closest("tr");
                    var data = table.row($row).data();
                    var rowId = data;
                    rows_selected.push(rowId);
                    var categoyryOrder = $('#category_order_'+rowId.id).val();
                    if(categoyryOrder < 0){
                        categoyryOrder = 0;
                    }
                    orderArr.push(rowId.id, categoyryOrder);
                    ids.push(orderArr);
                });

                $.ajax({
                    type: "POST",
                    url: $dataTable.data("update-order-url"),
                    data: { ids: ids },
                    beforeSend: function() {
                        //
                    },
                    success: function(response) {
                        data_table.ajax.reload(null, false);
                        rows_selected = [];
                        App.showNotification(response);
                    },
                    error: function() {},
                    complete: function() {
                        //
                    }
                });
            });
        },

        checkTags: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");
            var $dataTableWrapper = $(".dataTables_wrapper");

            // Handle form submission event
            $("#remove_tags_name").on("keyup", function() {
                var tagsName = $("#remove_tags_name").val();

                if(tagsName != '')
                {
                    data_table.ajax.reload(null, false);
                    $(".btn-remove-tags").prop("disabled", false);
                }
                else
                {
                    data_table.ajax.reload(null, false);
                    $(".btn-remove-tags").prop("disabled", true);
                }
            });
        },

        /**
         * Remove Tags.
         */
        removeTags: function() {
            var $data_table_container = $(".table-responsive");
            var $dataTable = $(".dataTable");
            var $dataTableWrapper = $(".dataTables_wrapper");

            // Handle form submission event
            $(".btn-remove-tags").on("click", function() {
                var tagsName = $("#remove_tags_name").val();

                if(tagsName != '')
                {
                    $.ajax({
                        type: "POST",
                        url: $dataTable.data("remove-tags-url"),
                        data: { tag_names: tagsName },
                        beforeSend: function() {
                            $(".btn-remove-tags").prop("disabled", true);
                        },
                        success: function(response) {
                            $("#remove_tags_name").val('');
                            data_table.ajax.reload(null, false);
                            App.showNotification(response);
                        },
                        error: function() {},
                        complete: function() {
                            $(".btn-remove-tags").prop("disabled", true);
                        }
                    });
                }
            });
        },
        // JS function ends
    };
})();

Products.init();
