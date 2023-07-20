@extends('admin.main')

@section('title', 'Dashboard')

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
						<h4 class="mb-sm-0 font-size-18">Dashboard</h4>
					</div>
				</div>
			</div>
			<!-- end page title -->

			<div class="row">
				<div class="col-xl-4">
					<div class="card overflow-hidden">
						<div class="bg-primary bg-soft">
							<div class="row">
								<div class="col-7">
									<div class="text-primary p-3">
										<h5 class="text-primary">Welcome Back !</h5>
										<p>{{ config('constants.APP_NAME') }}</p>
									</div>
								</div>
								<div class="col-5 align-self-end">
									<img src="{{ asset('admin/images/banners/profile-img.png') }}" alt="" class="img-fluid">
								</div>
							</div>
						</div>
						<div class="card-body pt-0">
							<div class="row">
								<div class="col-sm-8">
									<div class="avatar-md profile-user-wid mb-4">
										<img src="{{ asset('admin/images/admin-profile.png') }}" alt="" class="img-thumbnail rounded-circle">
									</div>
									<h5 class="font-size-15 text-truncate">{{ Auth::user()->name }}</h5>
									{{-- <p class="text-muted mb-0 text-truncate">{{ Auth::user()->roles->first()->name }}</p> --}}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xl-8">
					<div class="row">
                        <div class="col-md-4">
                            <a href="{{ route('admin.qr-codes.index') }}">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium mb-1">Total Coupons</p>
                                                <h5 class="mb-0">
                                                    <span id="totalCouponRedeemedCount">0</span> |
                                                    <span id="totalCouponCount">0</span>
                                                </h5>
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
                            </a>
                        </div>
                        @foreach($data['coupons'] as $coupon)
                        <div class="col-md-{{ 12/min(count($data['coupons']), 3) }}">
                            <a href="{{ route('admin.qr-codes.index', ['reward_id'=>$coupon->id]) }}">
                                <div class="card mini-stats-wid">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <p class="text-muted fw-medium mb-1">₹ {{ $coupon->value }} Coupons</p>
                                                <h5 class="mb-0">
                                                    <span  id="countRedeemedCoupon{{ $coupon->value }}">0</span> | 
                                                    <span  id="countCoupon{{ $coupon->value }}">0</span>
                                                </h5>
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
                            </a>
                        </div>
                        @endforeach
					</div>
					<!-- end row -->
				</div>
			</div>
            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.retailers.index',['filter-by-date' => 'today']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Total Users Today</p>
                                        <h5 class="mb-0" id="retailer-count-today"></h5>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.retailers.index',['filter-by-date' => 'last7days']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Users Last 7 Days</p>
                                        <h5 class="mb-0" id="retailer-count-last7days"></h5>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.retailers.index',['filter-by-date' => 'last30days']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Users Last 30 Days</p>
                                        <h5 class="mb-0" id="retailer-count-last30days"></h5>
                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.retailers.index',['filter-by-date' => 'last90days']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Users Last 90 Days</p>
                                        <h5 class="mb-0" id="retailer-count-last90days"></h5>

                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <a href="{{ route('admin.login-histories') }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Total Payouts</p>
                                        <h5 class="mb-0" id="total_payout"></h5>

                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{route('admin.payouts.index',['status' => 'success']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Success Payout</p>
                                        <h5 class="mb-0" id="success_payout"></h5>

                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-3">
                    <a href="{{ route('admin.payouts.index',['status' => 'pending']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Pending Payout</p>
                                        <h5 class="mb-0" id="pending_payout"></h5>

                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="{{ route('admin.payouts.index',['status' => 'failed']) }}">
                        <div class="card mini-stats-wid">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium mb-1">Failed Payouts</p>
                                        <h5 class="mb-0" id="failed_payouts"></h5>

                                    </div>

                                    <div class="flex-shrink-0 align-self-center">
                                        <div class="mini-stat-icon avatar-sm rounded-circle bg-primary">
                                            <span class="avatar-title">
                                                <i class="fas fa-user-friends font-size-18"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                Scanned Histories <i>(Latest 10)</i>
                            </h4>
                            <table class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>Retailer Name</th>
                                        <th>Serial Number</th>
                                        <th>Reward Value</th>
                                        <th>Scanned At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['scannedHistories'] as $scannedHistory)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.retailers.show',['retailer' => $scannedHistory->retailer_id]) }}">
                                                {{ $scannedHistory->retailer->name }}
                                            </a>
                                        </td>
                                        <td>{{ $scannedHistory->qRCodeItem->serial_number }}</td>
                                        <td>{{ $scannedHistory->qRCodeItem->rewardItem->value }}</td>
                                        <td>{{ $scannedHistory->created_at }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="4" align="center">
                                            <a href="{{ route('admin.login-histories') }}">View More</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                Top Scanned Users
                            </h4>
                            <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Mobile Number</th>
                                    <th>Count</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($data['topScannedUsers'] as $topScannedUser)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.retailers.show',['retailer' => $topScannedUser['retailerId']]) }}">
                                                {{ $topScannedUser['name'] }}
                                            </a>
                                        </td>
                                        <td>{{ $topScannedUser['mobile_number'] }}</td>
                                        <td>{{ $topScannedUser['count'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
			<!-- end row -->
		</div>
		<!-- End Page-content -->
	</div>
</div>
@endsection

@section('script')
<script>

    // user count
    $(function() {
        function getUserCount(range, container) {
            $.ajax({
                url: "{{ route('admin.retailer_count',['range'=>'']) }}"+range,
                dataType: 'json',
                success: function(data) {
                    $(container).text(data.count);
                }
            });
        }

        getUserCount('today', '#retailer-count-today');
        getUserCount('last7days', '#retailer-count-last7days');
        getUserCount('last30days', '#retailer-count-last30days');
        getUserCount('last90days', '#retailer-count-last90days');
        setInterval(function() {
            getUserCount('today', '#retailer-count-today');
            getUserCount('last7days', '#retailer-count-last7days');
            getUserCount('last30days', '#retailer-count-last30days');
            getUserCount('last90days', '#retailer-count-last90days');
        }, 10000);
    });

    // payout count
    $(function() {
        function getPayoutCount(status, container) {
            $.ajax({
                url: "{{ route('admin.payout_count',['status'=>'']) }}"+status,
                dataType: 'json',
                success: function(data) {
                    $(container).text(data.payoutamount);
                }
            });
        }

        getPayoutCount('success', '#success_payout');
        getPayoutCount('pending', '#pending_payout');
        getPayoutCount('failed', '#failed_payouts');
        getPayoutCount('total', '#total_payout');
        setInterval(function() {
            getPayoutCount('success', '#success-payout');
            getPayoutCount('pending', '#pending_payout');
            getPayoutCount('failed', '#failed_payouts');
            getPayoutCount('total', '#total_payout');
        }, 10000);
    });

    $(function() {
        function getCouponCount() {
            $.ajax({
                url: "{{ route('admin.get_coupon_count') }}",
                dataType: 'json',
                success: function(data) {
                    console.log(data.couponCounts)
                    $.each(data.couponCounts, function(key,value){
                        $("#"+key).text(value);
                    });
                }
            });
        }

        getCouponCount();
        setInterval(function() {
            getCouponCount();
        }, 10000);
    });
</script>
@endsection
