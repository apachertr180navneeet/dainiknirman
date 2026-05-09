@extends('admin.layout.login_app')

@section('title', 'Login')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href=""><b>Reset Password</b></a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <form action="{{ route('admin.forgotPassword.resetPassword', ['reset_token' => $code]) }}" method="post" id="LoginForm" autocomplete="form-no-fill">
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
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Enter New Password" autocomplete="0" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="0">
                    <div class="row">
                        <!-- /.col -->
                        <div class="col-12">
                            <input type="submit" class="btn btn-primary btn-block" value="Change Password">
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
