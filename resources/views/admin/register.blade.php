<!doctype html>
<html lang="en">

    <head>
        
        <meta charset="utf-8" />
        <title>Register | {{ env('APP_NAME') }}</title>
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
                                            <h5 class="text-primary">Register</h5>
                                            <p>Get your free {{ env('APP_NAME') }} now.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="{{ asset('admin/images/banners/profile-img.png') }}" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div>
                                    <a href="{{ url('/') }}">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset('admin/images/logo/logo.svg') }}" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <div class="needs-validation">
                                        <div class="mb-3">
                                            <p style="margin: 1rem 0 0;">
                                                <span id="MsgForLogin" class=""></span>
                                            </p>
                                        </div>
                
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" placeholder="Enter name">
                                            <span id="nameErr" class="text-danger" style="display:none;">Please enter name</span>
                                        </div>
            
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" placeholder="Enter email">
                                            <span id="emailErr" class="text-danger" style="display:none;">Please enter valid email address</span>
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" required="">
                                                <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            <span id="passwordErr" class="text-danger" style="display:none;">Please enter password</span>
                                            </div>
                                        </div>
                    
                                        <div class="mt-4 d-grid">
                                            <input type="submit" id="btnRegister" name="btnRegister" value="Register" class="btn btn-primary waves-effect waves-light">
                                        </div>
                
                                        <div class="mt-4 text-center">
                                            <p class="mb-0">By registering you agree to the Skote <a href="#" class="text-primary">Terms of Use</a></p>
                                        </div>
                                    </div>
                                </div>
            
                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            
                            <div>
                                <p>Already have an account ? <a href="{{ url('admin/login') }}" class="fw-medium text-primary"> Login</a> </p>
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

        <script type="text/javascript">
            function emailValid(email, emailErr){
                atpos = email.indexOf("@");
                dotpos = email.lastIndexOf(".");
                if(atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length){
                    emailErr.show();
                    return false;
                }
                emailErr.hide();
                return true;
            }
            //onkeypress attribute to be added with return keyword
            function NumberOnly(e){
                var k;
                document.all ? k = e.keyCode : k = e.which;
                return ((k > 47 && k < 58) || k == 8 || k == 0);
            }

            function contactValid(contact, contactErr){
                if(contact.length<10){
                    contactErr.show();
                    return false;                
                }
                contactErr.hide();
                return true;
            }

            function inputValid(inputs, inputsID, inputsErr){
                userInput = inputs;
                showError = inputsErr;
                if (userInput=='' || userInput==null || userInput=='-1' || userInput.length<=0) {
                    showError.show();
                    inputsID.focus();
                    inputsID.css('border-color','#dc3545');
                    return false;
                }
                showError.hide();
                inputsID.css('border-color','#ced4da');
                return true;
            }
        </script>
        <script>
            $(document).ready(function(){
                $("#btnRegister").on('click', function(){
                    $("#MsgFromServer").hide();
                    //data
                    url = "{{URL::to('/registerAdmin')}}";
                    name = $("#name").val();
                    email = $("#email").val();
                    password = $("#password").val();

                    //validation ID
                    nameID = $("#name");
                    emailID = $("#email");
                    passwordID = $("#password");

                    nameErr = $("#nameErr");
                    emailErr = $("#emailErr");
                    passwordErr = $("#passwordErr");

                    if(inputValid(name,nameID,nameErr)==false){
                        return false;
                    }
                    if(inputValid(email,emailID,emailErr)==false || emailValid(email, emailErr)==false){
                        return false;
                    }
                    if(inputValid(password,passwordID,passwordErr)==false){
                        return false;
                    }

                    data = {name:name, email:email, password:password,  '_token':'{{ csrf_token() }}'};

                    $.ajax({
                        url: url,
                        type:'POST',
                        data: data,
                        dataType: "JSON",
                        success:function(response){
                            if(response.status=='success'){
                                $(".alert.alert-danger").hide();
                                $res = response.message;
                                $("#password").val("");
                                $("#MsgFromServer").removeClass('text-danger');
                                $("#MsgFromServer").addClass('text-success');
                                $("#MsgFromServer").html($res).show();
                                window.location = "{{ url('admin/home') }}";
                            }
                            if(response.status=='failed'){
                                $(".alert.alert-danger").hide();
                                $res = response.message;
                                $("#MsgFromServer").removeClass('text-success');
                                $("#MsgFromServer").addClass('text-danger');
                                $("#MsgFromServer").html($res).show();
                            }
                        }
                    });
                });
            });
        </script>
    </body>
</html>
