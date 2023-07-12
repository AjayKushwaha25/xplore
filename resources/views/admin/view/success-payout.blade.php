@extends('admin.main')

@section('title', 'View Success Payouts')

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
                                <button type="button" class="btn btn-primary import">Add Payout</button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="mdi mdi-chevron-down"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.payouts.create') }}">Add Payout</a>
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadCSV">Add Bulk Payouts</a>
                                </div>
                            </div>
                        </div>
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
                                        <th>Reward Value</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                        <th>UTR</th>
                                        <th>Processed At</th>
                                        {{-- <th>Action</th> --}}
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

{{--  --}}
<div class="modal fade" id="uploadCSV" tabindex="-1" role="dialog" aria-labelledby="uploadCSVTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCSVTitle">Upload CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bulk_payout_upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p>Download a <a href="{{ asset('sample_csv/payout_sample_csv.csv') }}" download>sample CSV template</a> to see an example of the format required.</p>
                            <div class="mt-3">
                                <label for="formFile" class="form-label">Import CSV</label>
                                <input class="form-control" type="file" name="file" accept=".csv" required="">
                                <small>Only <code>.csv</code> files.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary waves-effect waves-light">Upload</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
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
            order: [ 0, 'desc' ],
            fixedHeader:{
                headerOffset: $('#page-topbar').outerHeight(),
                header: true
            },
            stateSave: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.success_payout') }}",
            columns: [
                { data: 'login_history.retailer.name', name: 'loginHistory.retailer.name' ,
                    render : function(data, type, row) {
                        url = '{{ route('admin.retailers.show',['retailer' => ':retailerID']) }}'.replace(':retailerID', row.login_history.retailer.id);
                        return '<a href="'+url+'">'+row.login_history.retailer.name+'</a>';
                    }
                },
                { data: 'login_history.q_r_code_item.serial_number', name: 'loginHistory.qRCodeItem.serial_number',
                    render : function(data, type, row) {
                        url = '{{ route('admin.qr-codes.show',['qr_code' => ':qrCodeId']) }}'.replace(':qrCodeId', row.login_history.q_r_code_item.id);
                        return  row.login_history.q_r_code_item.serial_number;
                    }
                },
                // { data: 'q_r_code_item.serial_number', name: 'qRCodeItem.serial_number'},
                { data: 'login_history.q_r_code_item.reward_item.value', name: 'loginHistory.qRCodeItem.rewardItem.value'},
                { data: 'status', name: 'status' ,
                    render : function(data, type, row) {
                        if(row.status == 1)
                            return '<span class="badge badge-pill badge-soft-success font-size-11">Processed</span>';
                        else if(row.status == 2)
                            return '<span class="badge badge-pill badge-soft-danger font-size-11">Processing</span>';
                        else
                            return '<span class="badge badge-pill badge-soft-danger font-size-11">Failed</span>';
                    }
                },
                { data: 'reason', name: 'reason'},
                { data: 'utr', name: 'utr'},
                { data: 'processed_at', name: 'processed_at' ,
                    render : function(data, type, row) {
                        var d = new Date(row.processed_at);
                        month = ("0" + (d.getMonth() + 1)).slice(-2);
                        day = ("0" + d.getDate()).slice(-2);
                        return d.getFullYear() + '-' + month + '-' + day + ' ' + d.toTimeString().split(' ')[0];
                    }
                },
                /*{ data: 'id',
                    render : function(data, type, row) {
                        editURL = '{{ route('admin.retailers.edit',['retailer' => ':retailerID']) }}'.replace(':retailerID', row.id);
                        urlEdit = '<a href="'+editURL+'" class="mr-3 text-info" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Edit"><i class="mdi mdi-pencil font-size-18"></i></a>'

                        url = '{{ route('admin.retailers.show',['retailer' => ':retailerID']) }}'.replace(':retailerID', row.id);
                        urlView = '<a href="'+url+'" class="mr-3 text-info" data-toggle="tooltip" data-placement="top" title="View" data-original-title="View"><i class="mdi mdi-eye font-size-18"></i></a>'

                        return urlEdit+urlView;
                    }
                },*/
            ]
        }),
        $(".dataTables_length select").addClass("form-select form-select-sm");


        $('.import').click(function(){
           window.location.href="{{ route('admin.payouts.create') }}";
        });
    });
</script>
@endsection
