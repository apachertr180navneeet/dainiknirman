@extends('admin.layout.login_app')

@section('title', 'Login')

@section('content')
    <div class="login-box">
        <div class="login-logo">
            <a href=""><b>Admin</b>Login</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <form action="{{ route('admin.login.post') }}" method="post" id="LoginForm">
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
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="type" value="0">
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" name="remember" value="1" />
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <input type="submit" class="btn btn-primary btn-block" value="Sign In">
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mb-1">
                    <a href="{{ route('admin.forgotPassword') }}">I forgot my password</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
@endsection
