<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Login | {{ config('constants.APP_NAME') }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{ asset('admin/images/favicon.ico') }}">

        <!-- Bootstrap Css -->
        <link href="{{ asset('admin/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{ asset('admin/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body>
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Sign in to continue to {{ config('constants.APP_NAME') }}.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ asset('admin/images/banners/profile-img.png') }}" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div class="auth-logo">
                                    <a href="{{ route('admin.login') }}" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset('admin/images/logo/logo.svg') }}" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" action="{{ route('admin.check_login') }}" method="POST">
                                        @csrf
                                        @if (\Session::get('status')=='failed')
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{!! \Session::get('message') !!}</p>
                                            </div>
                                        </div>
                                        @endif
                                        @error('email')
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{{ $message }}</p>
                                            </div>
                                        </div>
                                        @enderror

                                        @error('password')
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{{ $message }}</p>
                                            </div>
                                        </div>
                                        @enderror
        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required="">
                                        </div>
                
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" name="password" value="{{ old('password') }}" id="password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" required="">
                                                <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember-check" value="checked" name="remember">
                                            <label class="form-check-label" for="remember-check">
                                                Remember me
                                            </label>
                                        </div>
                                        
                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <a href="{{ route('admin.login') }}" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                        </div>
                                    </form>
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            
                            <div>
                                <p>Don't have an account ? <a href="{{ url('admin/register') }}" class="fw-medium text-primary"> Signup now </a> </p>
                                <p>&copy; {{ date('Y') }} Admin Panel. Crafted with <i class="mdi mdi-heart text-danger"></i> by Ottoedge</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

        <!-- JAVASCRIPT -->
        <script src="{{ asset('admin/js/jquery.min.js') }}"></script>
        <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('admin/js/metisMenu.min.js') }}"></script>
        <script src="{{ asset('admin/js/simplebar.min.js') }}"></script>
        <script src="{{ asset('admin/js/waves.min.js') }}"></script>
        
        <!-- App js -->
        <script src="{{ asset('admin/js/app.js') }}"></script>
    </body>
</html>
