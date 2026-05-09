<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{$siteTitle ?? ''}} | @yield('title')</title>
         <!-- Google Font: Source Sans Pro -->
         <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" />
         <!-- Font Awesome -->
         <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css'); }}" />
         <!-- Ionicons -->
         <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
         <!-- Tempusdominus Bootstrap 4 -->
         <link rel="stylesheet" href="{{ asset('public/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css'); }}" />
         <!-- iCheck -->
         <link rel="stylesheet" href="{{ asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css'); }}" />
         <!-- JQVMap -->
         <link rel="stylesheet" href="{{ asset('public/plugins/jqvmap/jqvmap.min.css'); }}" />
         <!-- Theme style -->
         <link rel="stylesheet" href="{{ asset('public/dist/css/adminlte.min.css'); }}" />
         <!-- overlayScrollbars -->
         <link rel="stylesheet" href="{{ asset('public/plugins/overlayScrollbars/css/OverlayScrollbars.min.css'); }}" />
         <!-- Daterange picker -->
         <link rel="stylesheet" href="{{ asset('public/plugins/daterangepicker/daterangepicker.css'); }}" />
         <!-- summernote -->
         <link rel="stylesheet" href="{{ asset('public/plugins/summernote/summernote-bs4.min.css'); }}" />
         <!-- Select2 -->
         <!-- <link rel="stylesheet" href="{{ asset('public/plugins/select2/css/select2.min.css'); }}" /> -->
         <link rel="stylesheet" href="{{ asset('public/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css'); }}" />
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
         <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
         <!-- Bootstrap Select CSS -->
        <link href="{{ asset('public/plugins/bootstrap-select/bootstrap-select.min.css') }}" rel="stylesheet"/>

        <link href="{{ asset('public/css/custom.css') }}" rel="stylesheet" type="text/css" />

        <!-- jQuery -->
        <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>
        <style>
            div#customer_list_filter{
                float: right;
            }

            .tableimg{
                width: 7%;
            }
        </style>
        @stack('styles')
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->
            
            @extends('admin.layout.sidebar')

            <!-- Content Wrapper. Contain-s page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <!-- <h1 class="m-0"></h1> -->
                                <h4 class="m-0">{{$pageTitle ?? ''}}</h4>
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-6">
                                @if(isset($breadcrumb))
                                    <ol class="breadcrumb float-sm-right">
                                        @foreach($breadcrumb as $key => $value)
                                            @if(!empty($value))
                                                <li class="breadcrumb-item active">
                                                    <a href="{{$value}}">{{$key}}</a>
                                                </li>
                                            @else
                                                @if(!request()->is(Request::segment(1).'/dashboard*') && request()->route()->getName() != 'adminPanel.dashboard')
                                                <li class="breadcrumb-item">
                                                    <span>{{$key}}</span>
                                                </li>
                                                @endif
                                            @endif
                                        @endforeach
                                    </ol>
                                @endif
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- /.container-fluid -->
                </section>
                <!-- /.content-header -->

                @yield('content')
            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <strong>Copyright &copy; {{date('Y')}} <a href="">JaiHarsh</a>.</strong>
                All rights reserved.
            </footer>

            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Control sidebar content goes here -->
            </aside>
            <!-- /.control-sidebar -->

        </div>

        <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}"></script>
        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('public/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <script>
            $.widget.bridge('uibutton', $.ui.button)
        </script>
        <!-- Bootstrap 4 -->
        <script src="{{ asset('public/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('public/plugins/chart.js/Chart.min.js') }}"></script>
        <script src="{{ asset('public/plugins/sparklines/sparkline.js') }}"></script>
        <script src="{{ asset('public/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
        <script src="{{ asset('public/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
        <script src="{{ asset('public/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
        <!-- Select2 -->
        <!-- <script src="{{ asset('public/plugins/select2/js/select2.full.min.js') }}"></script> -->
        <!-- <script src="{{ asset('public/plugins/jquery-knob/jquery.knob.min.js') }}"></script> -->
        <!-- Select2 js-->
        <script src="{{ asset('public/plugins/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('public/plugins/bootstrap-select/bootstrap-select.min.js') }}"></script>
        <!-- DataTables -->
        <script src="{{ asset('public/plugins/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('public/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('public/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('public/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
        <!-- DataTables Buttons JS -->

        <!--sweet alert-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

        <script src="{{ asset('public/plugins/summernote/summernote-bs4.min.js') }}"></script>
        <script src="{{ asset('public/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
        <!-- daterange picker -->
        <script src="{{ asset('public/plugins/moment/moment.min.js') }}"></script>
        <script src="{{ asset('public/plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('public/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
        <!-- AdminLTE App -->
        {{--  <script src="{{ asset('public/dist/js/adminlte.min.js') }}"></script>  --}}
        <!-- AdminLTE App -->
        <script src="{{ asset('public/dist/js/adminlte.js') }}"></script>
        <!-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script> -->

        <!-- Jquery Form Validation Plugin -->
        <script src="{{ asset('public/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('public/plugins/jquery-validation/additional-methods.min.js') }}"></script>
        <script src="{{ asset('public/plugins/jquery-validation/jquery.validate.file.js') }}"></script>

        <!-- App JS -->
        <script src="{{ asset('public/js/app.js') }}"></script>
        <script>
        @if (Session::has('notification'))
            var notification = @json(Session::get('notification'));

            // Show notification
            $(document).ready(function () {
                App.showNotification(notification);
            });
            //------------------
        @endif
        </script>

        @stack('scripts')

    </body>

</html>

