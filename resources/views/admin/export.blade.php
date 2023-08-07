@extends('admin.main')

@section('title', 'Export')

@section('style')
<link href="{{ asset('admin/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
	.main-content{
		height: 100vh;
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
                @if (\Session::has('table'))
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p style="margin-bottom: 0;">{!! \Session::get('table') !!}</p>
                    </div>
                </div>
                @endif
                @if($errors->has('start_date'))
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p style="margin-bottom: 0;">{{ $errors->first('start_date') }}</p>
                    </div>
                </div>
                @endif
                @if($errors->has('end_date'))
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p style="margin-bottom: 0;">{{ $errors->first('end_date') }}</p>
                    </div>
                </div>
                @endif
                @if($errors->has('classNotFoundError'))
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p style="margin-bottom: 0;">{{ $errors->first('classNotFoundError') }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="row">
            	<form action="{{ url('admin/exportData') }}" method="POST">
            		@csrf
	                <div class="col-md-8">
	                    <div class="card">
	                        <div class="card-body">
	                            <div class="row">
	                                <div class="col-md-12">
	                                    <div class="mb-3">
	                                        <label for="table">Tables.:</label>
	                                        <select name="table" id="table" class="form-select select2" required="">
	                                        	<option value="">-- Select Tables --</option>
	                                            @foreach($data['tables'] as $table)
		                                        @if(!in_array($table, $data['ignoreTables']))
	                                            <option value="{{ $table }}" {{ old('table')==$table ? 'selected' : '' }}>{{ CustomHelper::camelCase2String($table) }}</option>
	                                            @endif
	                                            @endforeach
                                                {{-- <option value="payouts" {{ old('table')=='payout' ? 'selected' : '' }}>Payouts</option> --}}
	                                        </select>
	                                    </div>
	                                </div>
	                            </div>
								<div class="row">
	                                <div class="col-md-12">
	                                    <div class="mb-3">
	                                        <label for="region">Region:</label>
	                                        <select name="region" id="region" class="form-select select2">
	                                        	<option value="">-- Select Region --</option>
                                                <option value="all" >All</option>
	                                            @foreach($data['region'] as $region)
	                                            <option value="{{ $region->id }}" >{{ $region->name }}</option>
	                                            @endforeach
	                                        </select>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="row">
	                                <div class="col-md-12">
	                                    <div class="mb-3">
	                                        <label for="brandName">Date.:</label>
	                                    	<div class="input-daterange input-group" id="datepicker6" data-date-format="yyyy-mm-dd" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker6'>
	                                            <input type="text" class="form-control" name="start_date" placeholder="Start Date" required="" value="{{ old('start_date') }}" />
	                                            <input type="text" class="form-control" name="end_date" placeholder="End Date" required="" value="{{ old('end_date') }}"/>
	                                        </div>
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
	            </form>
            </div>
		</div>
		<!-- End Page-content -->
	</div>
</div>
@endsection

@section('script')

<script src="{{ asset('admin/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
