<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{$siteTitle ?? ''}} | @yield('title')</title>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="{{ asset('public/plugins/fontawesome-free/css/all.min.css'); }}">
        <!-- icheck bootstrap -->
        <link rel="stylesheet" href="{{ asset('public/plugins/icheck-bootstrap/icheck-bootstrap.min.css'); }}">
        <!-- Theme style -->
        <link rel="stylesheet" href="{{ asset('public/dist/css/adminlte.min.css'); }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    </head>
    <body class="hold-transition login-page">
       <div class="login-box">
        <div class="login-logo">
            <a href="javascript:;">Delete Account</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <form action="{{ route('front.user.deleteMyAccount') }}" method="post" id="LoginForm">
                    @if ($errors->any())
                    <div class="alert alert-danger mb-4" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <i data-feather="x" class="feather-16"></i> </button>
                        <strong>Error!</strong>
                        @foreach ($errors->all() as $error)
                            <div> {{ $error }} </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <!--@csrf-->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="input-group mb-3">
                        <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number" autocomplete="off" data-check-url="{{route('front.user.checkAccount')}}"/>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-mobile"></span>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="input-group mb-3 _d-none">
                        <input type="password" name="otp" id="otp" class="form-control" placeholder="Enter OTP" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary btn-block" value="Delete Account">
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
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
       <!-- App JS -->
        <script src="{{ asset('public/js/app.js') }}"></script>

        <!--sweet alert-->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

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

        <script>
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            });
            async function checkMobileNumber(){
                return await $.ajax({
                    type: "POST",
                    url: $('#mobile').data('check-url'),
                    async: true,
                    data: {mobile: $('#mobile').val() },
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                    },
                    // success: function(response) {
                    //     if(response._status){
                    //         return true;
                    //     }
                    //     else{
                    //         App.showNotification(response);
                    //         return false;
                    //     }
                    // },
                });
            }
        $(function () {
            // $.validator.setDefaults({
            //     submitHandler: function () {
            //         return true;
            //     },
            // });
            $("#LoginForm").validate({
                rules: {
                    mobile: {
                        required: true,
                        number: true,
                        minlength: 10,
                        maxlength: 10
                    }
                },
                messages: {
                    mobile: {
                        required: "Please enter mobile number.",
                        number: "Please enter a valid mobile number",
                        minlength: "Please enter a valid 10 digit mobile number",
                        maxlength: "Please enter a valid 10 digit mobile number",
                    }
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
                submitHandler: function(form) {
                    App.formLoading($("#LoginForm"));
                    var res = checkMobileNumber()
                    .then(data => {
                        console.log(data)
                        if(data._status)
                        {
                            Swal.fire({
                                title: "Are you sure to delete your account?",
                                text: "You won't be able to revert this!",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, delete it!"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        }
                        else{
                            App.showNotification(data);
                            return false;
                        }
                    })
                    .catch(err => console.error("Handled outside:", err));
                    
                    // form.submit();
                }
            });
        });
       </script>
    </body>
</html>
