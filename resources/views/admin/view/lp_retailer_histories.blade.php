@extends('admin.main')

@section('title', 'View LP Retailer History')

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
                                        <th>Retailer name</th>
                                        <th>Coupon code</th>   
                                        <th>Reward Value</th>                                    
                                        <th>Created At</th>
                                        <!-- <th>Action</th> -->
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
        // id = 0
        $('#datatable').DataTable({
            responsive: true,
            order: [ 3, 'desc' ],
            fixedHeader:{
                headerOffset: $('#page-topbar').outerHeight(),
                header: true
            },
            stateSave: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.lpretailer_history_list') }}",
            columns: [
                { data: 'lp_retailer.name', name: 'lpRetailer.name'},
                { data: 'coupon_code.code', name: 'couponCode.code'},
                { data: 'coupon_code.reward_item.value', name: 'couponCode.rewardItem.value'},
                { data: 'created_at', name: 'created_at' ,
                    render : function(data, type, row) {
                        var d = new Date(row.created_at);
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
    });
</script>
@endsection
