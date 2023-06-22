@extends('admin.main')

@section('title', 'View Admin')

@section('style')
	<!-- data table -->
	<link rel="stylesheet" href="{{ asset('admin/css/dataTables.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/css/responsive.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/css/fixedHeader.bootstrap.min.css') }}">
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
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                	@foreach($data['users'] as $user)
	                                <tr>
	                                    <td>{{ $user->name }}</td>
	                                    <td>{{ $user->email }}</td>
	                                    <td>
								            @foreach($user->roles as $role)
								            {{ $role->name }}
								            @endforeach
	                                    </td>
	                                    <td>
	                                    	@if($user->status == 1)
	                                    	<span class="badge badge-pill badge-soft-success font-size-11">Active</span>
	                                    	@else
	                                    	<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>
	                                    	@endif
	                                    </td>
	                                    <td>{{ $user->created_at }}</td>
	                                    <td>
                                            <a href="{{ route('admin.users.edit',['user' => $user->id])}}" class="mr-3 text-primary" data-toggle="tooltip" data-placement="top" title="" data-original-title="Edit"><i class="mdi mdi-pencil font-size-18"></i></a>
                                        </td>
	                                </tr>
	                                @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
		</div>
	</div>
</div>
@endsection

@section('script')
<script src="{{ asset('admin/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/js/dataTables.bootstrap4.min.js') }}"></script>

<script src="{{ asset('admin/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/js/dataTables.fixedHeader.min.js') }}"></script>
<script src="{{ asset('admin/js/datatables.init.js') }}"></script>
@endsection
