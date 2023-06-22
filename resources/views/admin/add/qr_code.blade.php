@php
use \Illuminate\Support\Facades\File;
$path = 'images/printables';
$publicPath = public_path($path);
@endphp
@extends('admin.main')

@section('title', 'Generate QR Code')

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
            @if (\Session::has('job-queued'))
            <div class="mb-3">
                <div class="alert alert-warning">
                    <p style="margin-bottom: 0;">{!! \Session::get('job-queued') !!}</p>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <form action="{{ route('admin.qr-codes.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-3">Bulk QR Code</h4>
                                        @if (\Session::has('success'))
                                        <div class="mb-3">
                                            <div class="alert alert-success">
                                                <p style="margin-bottom: 0;">{!! \Session::get('success') !!}</p>
                                            </div>
                                        </div>
                                        @endif
                                        @if (\Session::has('message'))
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{!! \Session::get('message') !!}</p>
                                            </div>
                                        </div>
                                        @endif
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

                                        @error('file')
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{{ $message }}</p>
                                            </div>
                                        </div>
                                        @enderror
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
                                            <div class="col-md-12">
                                                <div class="mb-3 row">
                                                    <label for="formFile" class="col-md-2 col-form-label">Upload File</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="file" name="file" required>
                                                        <small>Download a <a href="{{ asset('sample_csv/qr_code_sample_csv.csv') }}">sample CSV template</a> to see an example of the format required.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button style="z-index: 9;margin-right: 20px;margin-bottom: 20px;"  class="btn btn-success waves-effect waves-light">Generate Bulk QR Code</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <form action="{{ route('admin.qr-codes.bulk-update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-3">Update key</h4>
                                        @if (\Session::has('success'))
                                        <div class="mb-3">
                                            <div class="alert alert-success">
                                                <p style="margin-bottom: 0;">{!! \Session::get('success') !!}</p>
                                            </div>
                                        </div>
                                        @endif
                                        @if (\Session::has('message'))
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{!! \Session::get('message') !!}</p>
                                            </div>
                                        </div>
                                        @endif
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

                                        @error('file')
                                        <div class="mb-3">
                                            <div class="alert alert-danger">
                                                <p style="margin-bottom: 0;">{{ $message }}</p>
                                            </div>
                                        </div>
                                        @enderror
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
                                            <div class="col-md-12">
                                                <div class="mb-3 row">
                                                    <label for="formFile" class="col-md-2 col-form-label">Upload File</label>
                                                    <div class="col-md-10">
                                                        <input class="form-control" type="file" name="file" required>
                                                        <small>Download a <a href="{{ asset('sample_csv/qr_code_sample_csv.csv') }}">sample CSV template</a> to see an example of the format required.</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button style="z-index: 9;margin-right: 20px;margin-bottom: 20px;"  class="btn btn-success waves-effect waves-light">Generate Bulk QR Code</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Page-content -->
    </div>
</div>
@endsection

@section('script')
@endsection
