@extends('admin.main')

@section('title', "{$qr_code->serial_number}'s Details")

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
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                @yield('title')
                                <a href="{{ route('admin.qr-codes.edit', $qr_code->id) }}">
                                    <i class="mdi mdi-square-edit-outline font-size-18"></i>
                                </a>
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-nowrap mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row">Name :</th>
                                            <td>{{ $qr_code->name }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Mobile Number :</th>
                                            <td>{{ $qr_code->mobile_number }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Whatsapp Number :</th>
                                            <td>{{ $qr_code->whatsapp_number }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">UPI ID :</th>
                                            <td>{{ $qr_code->upi_id }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Registered At :</th>
                                            {{-- <td>{{ $qr_code->created_at->diffForHumans() }}</td> --}}
                                            <td>{{ $qr_code->created_at->format('j F Y h:s:i A') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-1">Total Coupons Scanned</p>
                                            <h5 class="mb-0">{{ $scannedHistories->count() }}</h5>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bxs-coupon font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-1">Total Earnings</p>
                                            <h5 class="mb-0">₹ {{ $rewardItemValue }}</h5>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bxs-coupon font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="card mini-stats-wid">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <p class="text-muted fw-medium mb-1">Pending Payouts</p>
                                            <h5 class="mb-0">₹ 20</h5>
                                        </div>

                                        <div class="flex-shrink-0 align-self-center">
                                            <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                                <span class="avatar-title">
                                                    <i class="bx bxs-coupon font-size-24"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div> <!-- end row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                <tr>
                                    <th>Serial Number</th>
                                    <th>Reward Value</th>
                                    <th>Scanned At</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($scannedHistories as $scannedHistory)
                                    <tr>
                                        <td>{{ $scannedHistory->qRCodeItem->serial_number }}</td>
                                        <td>{{ $scannedHistory->qRCodeItem->rewardItem->value }}</td>
                                        <td>{{ $scannedHistory->created_at }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div>
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
            order: [ 2, 'desc' ],
            fixedHeader:{
                headerOffset: $('#page-topbar').outerHeight(),
                header: true
            },
        }),
        $(".dataTables_length select").addClass("form-select form-select-sm");
    });
</script>
@endsection
