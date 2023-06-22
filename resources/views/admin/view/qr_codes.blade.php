@extends('admin.main')

@section('title', 'View QR Codes')

@section('style')
    <!-- data table -->
    <link rel="stylesheet" href="{{ asset('admin/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/fixedHeader.bootstrap.min.css') }}">
    <style type="text/css">
        .dropdown-item{
            cursor: pointer;
        }
    </style>
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

                        <div class="page-title-right">
                            <!-- Example split danger button -->
                            <div class="btn-group">
                                {{-- <button type="button" class="btn btn-primary addOutlet">Gen</button> --}}
                                <a href="{{ route('admin.qr-codes.create') }}" class="btn btn-primary">Generate QR Code</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            @if ($message = Session::get('upload-success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if ($message = Session::get('upload-empty'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>{{ $message }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if ($messages = Session::get('upload-failed'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ol>
                    @foreach($messages['errors'] as $error)
                    <li>At row {{ $messages['rows'] }}, {{ $error }}</li>
                    @endforeach
                </ol>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Serial Number</th>
                                        <th>WD Code</th>
                                        <th>URL</th>
                                        <th>Coupon</th>
                                        <th>Reward Amount</th>
                                        <th>Is Redeemed?</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
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
            order: [ 7, 'desc' ],
            fixedHeader:{
                headerOffset: $('#page-topbar').outerHeight(),
                header: true
            },
            stateSave: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.qr_code_lists', ['reward_id'=>request()->get('reward_id')]) }}",
            columns: [
                { data: 'serial_number', name: 'serial_number' },
                { data: 'wd.code', name: 'wd.code' },
                { data: 'url', name: 'url' ,
                    render : function(data, type, row) {
                        url = row.url;
                        return '<a href="'+url+'" target="_blank">Link</a>';
                    }
                },
                { data: 'path', name: 'path' ,
                    render : function(data, type, row) {
                        var imgURL = "{{ \Storage::disk('public')->url(':filename') }}".replace(':filename', row.path);
                        return '<a href="'+imgURL+'" target="_blank">View Coupon</a>';
                    }
                },
                { data: 'reward_item.value', name: 'rewardItem.value', defaultContent: '<i>NA</i>' },
                { data: 'is_redeemed', name: 'is_redeemed' ,
                    render : function(data, type, row) {
                        if(row.is_redeemed == 1)
                            return '<span class="badge badge-pill badge-soft-success font-size-11">Redeemed</span>';
                        else
                            return '<span class="badge badge-pill badge-soft-warning font-size-11">Pending</span>';
                    }
                },
                { data: 'status', name: 'status' ,
                    render : function(data, type, row) {
                        if(row.status == 1)
                            return '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                        else
                            return '<span class="badge badge-pill badge-soft-danger font-size-11">Inactive</span>';
                    }
                },
                { data: 'created_at', name: 'created_at' ,
                    render : function(data, type, row) {
                        var d = new Date(row.created_at);
                        month = ("0" + (d.getMonth() + 1)).slice(-2);
                        day = ("0" + d.getDate()).slice(-2);
                        return d.getFullYear() + '-' + month + '-' + day + ' ' + d.toTimeString().split(' ')[0];
                    }
                },
                { data: 'id',
                    render : function(data, type, row) {
                        editURL = '{{ route('admin.qr-codes.edit',['qr_code' => ':qrCodeID']) }}'.replace(':qrCodeID', row.id);
                        urlEdit = '<a href="'+editURL+'" class="mr-3 text-info" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit"><i class="mdi mdi-pencil font-size-18"></i></a>'

                        url = '{{ route('admin.qr-codes.show',['qr_code' => ':qrCodeID']) }}'.replace(':qrCodeID', row.id);
                        urlView = '<a href="'+url+'" class="mr-3 text-info" data-toggle="tooltip" data-placement="top" title="View" data-original-title="View"><i class="mdi mdi-eye font-size-18"></i></a>'

                        return urlEdit+urlView;
                    }
                },
            ]
        }),
        $(".dataTables_length select").addClass("form-select form-select-sm");
    });
</script>
@endsection
