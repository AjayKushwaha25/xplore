@extends('admin.main')

@section('title', 'Edit Admin')

@section('style')
@endsection

@section('content')
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
	<div class="page-content">
		<div class="container-fluid">
			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">@yield('title')</h4>
					</div>
				</div>
			</div>
			<!-- end page title -->
			
            <div class="row">
                <div class="col-12">
                    <div id="MsgFromServer" class="">
                        <p style="margin-bottom: 0;">{!! \Session::get('message') !!}</p>
                    </div>                    
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="name">Name.:</label>
                                        <input class="form-control" id="name" name="name"  value="{{ $data['userDetails']->name }}"/>
                                        <span id="nameErr" class="text-danger" style="display: none;">Please Enter Name</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="email">Email.:</label>
                                        <input class="form-control" id="email" name="email" value="{{ $data['userDetails']->email }}" />
                                        <span id="emailErr" class="text-danger" style="display: none;">Please Enter Valid Email</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                	<div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" class="form-control" name="password" value="{{ old('password') }}" id="password" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" autocomplete="new-password">
                                            <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
	                                        <span id="passwordErr" class="text-danger" style="display: none;">Please Enter Password</span>
	                                        <span id="passwordInvalidErr" class="text-danger" style="display: none;">Please should be greater than 8 characters</span>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="role">Role.:</label>
                                        <select class="form-select" id="role" name="role">
                                            <option value="-1">-- Select Role --</option>
                                            @foreach($data['roles'] as $id => $role)
                                            @if($data['userDetails']->roles->contains($id))
                                            <option value="{{ $id }}" selected>{{ $role }}</option>
                                            @else
                                            <option value="{{ $id }}">{{ $role }}</option>
                                            @endif
                                            {{-- <option value="super_admin">Super Admin</option>
                                            <option value="admin">Admin</option>
                                            <option value="staff">Counter Head</option> --}}
                                            @endforeach
                                        </select>
                                        <span id="roleErr" class="text-danger" style="display: none;">Please Select anyone</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Status</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <select name="activeStatus" id="activeStatus" class="form-select">
                                            <option value="1" {{ $data['userDetails']->status == "1" ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ $data['userDetails']->status == "0" ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        {{-- <small>This product will be hidden from all sales channels.</small> --}}
                                        <span id="activeStatusErr" class="text-danger" style="display: none;">Please select anyone</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="col-md-12">
                    <input type="submit" id="btnSubmit" style="z-index: 9;margin-right: 20px;margin-bottom: 20px;" name="btnSubmit" value="Submit" class="btn btn-success float-right">
                </div>
            </div>
		</div>
		<!-- End Page-content -->
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        $("#btnSubmit").on('click', function(){
            $("#MsgFromServer").hide();
            url = "{{ route('admin.users.update', ['user' => $data['userDetails']->id])}}";
            name = $("#name").val();
            email = $("#email").val();
            password = $("#password").val();
            role = $("#role").val();
            status = $("#activeStatus").val();

            nameID = $("#name");
            emailID = $("#email");
            passwordID = $("#password");
            roleID = $("#role");
            statusID = $("#activeStatus");

            //validation ID
            nameErr = $("#nameErr");
            emailErr = $("#emailErr");
            passwordErr = $("#passwordErr");
            roleErr = $("#roleErr");
            statusErr = $("#activeStatusErr");

            if(inputValid(name,nameID,nameErr)==false){
                return false;
            }
            if(inputValid(email,emailID,emailErr)==false){
                return false;
            }
            if(inputValid(password,passwordID,passwordErr)==false){
                return false;
            }
            if(inputValid(role,roleID,roleErr)==false){
                return false;
            }
            if(inputValid(status,statusID,statusErr)==false){
                return false;
            }

            data = {name:name,email:email,password:password,status:status,role:role, '_token':'{{ csrf_token() }}'};

            $.ajax({
                url: url,
                type:'PUT',
                data: data,
                dataType: "JSON",
                success:function(response){
                    if(response.status=='success'){
                        $res = response.message;
                        $("#MsgFromServer").removeClass('alert alert-danger');
                        $("#MsgFromServer").addClass('alert alert-success');
                        $("#MsgFromServer").html($res).show();
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                        setTimeout(function(){ 
                            window.location.href = "{{ route('admin.users.index') }}";
                        }, 2000);
                    }
                    if(response.status=='failed'){
                        $res = response.message;
                        $("#MsgFromServer").removeClass('alert alert-success');
                        $("#MsgFromServer").addClass('alert alert-danger');
                        $("#MsgFromServer").html($res).show();
                        $("html, body").animate({ scrollTop: 0 }, "slow");
                    }
                }
            });
        });
    });
</script>
@endsection
