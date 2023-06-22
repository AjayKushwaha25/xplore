@extends('admin.main')

@section('title', 'Edit QR Code')

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
                                        <label for="serial_number">Serial Number.:</label>
                                        <input class="form-control" id="serial_number" name="serial_number"  value="{{ $data['qRCodeItems']->serial_number }}" disabled />
                                        <span id="nameErr" class="text-danger" style="display: none;">Please Enter Name</span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="key">Key.:</label>
                                        <input class="form-control" id="key" name="key" value="{{ $data['qRCodeItems']->key }}" />
                                        <span id="emailErr" class="text-danger" style="display: none;">Please Enter Valid Email</span>
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
            url = "{{ route('admin.qr-codes.update', ['qr_code' => $data['qRCodeItems']->id])}}";
            serial_number = $("#serial_number").val();
            qrKey = $("#key").val();

            serial_numberID = $("#serial_number");
            qrKeyID = $("#key");

            //validation ID
            serial_numberErr = $("#nameErr");
            qrKeyErr = $("#emailErr");

            if(inputValid(serial_number,serial_numberID,serial_numberErr)==false){
                return false;
            }
            if(inputValid(key,qrKeyID,qrKeyErr)==false){
                return false;
            }

            data = {serial_number:serial_number,key:qrKey, '_token':'{{ csrf_token() }}'};

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
                            window.location.href = "{{ route('admin.qr-codes.index') }}";
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
