@extends('admin.main')

@section('title', 'View Retailer')

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
                                        <th>Retailer Name</th>
                                        <th>QR Code Scanned</th>
                                        <th>IP Address</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['loginHistory'] as $loginHistory)
                                    <tr>
                                        <td>{{ $loginHistory->name }}</td>
                                        <td>{{ $loginHistory->q_r_code_scanned }}</td>
                                        <td>{{ $loginHistory->ip_address }}</td>
                                        <td>{{ $loginHistory->created_at }}</td>
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
{{-- <script src="{{ asset('admin/js/datatables.init.js') }}"></script> --}}
<script type="text/javascript">
    $(document).ready(function(){
        id = 0
        $('#datatable').DataTable({
            responsive: true,
            columnDefs: [ {
                className: 'dtr-control',
                orderable: false,
                targets:   -1
            } ],
            order: [ 3, 'desc' ],
            fixedHeader:{
                headerOffset: $('#page-topbar').outerHeight(),
                header: true
            }
        }),
        $(".dataTables_length select").addClass("form-select form-select-sm");
    });
</script>
@endsection
