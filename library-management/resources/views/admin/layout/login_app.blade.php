<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>{{$siteTitle ?? ''}} | @yield('title')</title>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css'); }}">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="{{ asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css'); }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('public/dist/css/adminlte.min.css'); }}">
    </head>
    <body class="hold-transition login-page">
       @yield('content')
       <!-- jQuery -->
       <script src="{{ asset('public/plugins/jquery/jquery.min.js') }}" type="text/javascript"></script>
       <!-- Bootstrap 4 -->
       <script src="{{ asset('public/plugins/bootstrap/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
       <!-- jquery-validation -->
       <script src="{{ asset('public/plugins/jquery-validation/jquery.validate.min.js') }}" type="text/javascript"></script>
       <script src="{{ asset('public/plugins/jquery-validation/additional-methods.min.js') }}" type="text/javascript"></script>
       <!-- AdminLTE App -->
       <script src="{{ asset('public/dist/js/adminlte.min.js') }}" type="text/javascript"></script>
       <!-- AdminLTE for demo purposes -->
       <script src="{{ asset('public/dist/js/demo.js') }}" type="text/javascript"></script>
       <script>
        $(function () {
            $.validator.setDefaults({
                submitHandler: function () {
                    return true;
                },
            });
            $("#LoginForm").validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    terms: {
                        required: true,
                    },
                },
                messages: {
                    email: {
                        required: "Please enter a email address",
                        email: "Please enter a valid email address",
                    },
                    password: {
                        required: "Please provide a password",
                        minlength: "Your password must be at least 8 characters long",
                    },
                    terms: "Please accept our terms",
                },
                errorElement: "span",
                errorPlacement: function (error, element) {
                    error.addClass("invalid-feedback");
                    element.closest(".input-group").append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass("is-invalid");
                },
            });
        });
       </script>
    </body>
</html>
