@extends('main')

@section('title', 'Login')

@section('style')
    <!-- Icons Css -->
    <link href="{{ asset('admin/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .content .head-title{
        margin-top: 100%;
    }
</style>
@endsection

@section('content')
<div class="bg couponCodebg">
    <div class="content">
        <a href="{{ route('check_balance.login') }}" class="check-balance">
            <i class="fas fa-landmark" aria-hidden="true"></i>
            Check Balance
        </a>
        <div>
            <h1 class="head-title text-center">HELLO!</h1>
            <p class="mb-0 text-center">sign in your account</p>
        </div>
        <form class="form-sec" action="{{ route('check.login') }}" method="POST">
            @csrf
            <div class="mb-2">
                <input type="text" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" id="username" placeholder="Enter Name" name="name" required>
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>
            <div class="mb-2">
                <input type="number" maxlength="10" class="form-input @error('mobile_number') is-invalid @enderror" value="{{ old('mobile_number') }}" id="phone" placeholder="Phone No." pattern="[1-9]{1}[0-9]{9}" name="mobile_number" required>
                @error('mobile_number')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>
            <div class="mb-2">
                <input type="number" class="form-input @error('coupon_code') is-invalid @enderror" value="{{ old('coupon_code') }}" id="couponcode" placeholder="Enter coupon code" name="coupon_code">
                @error('coupon_code')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>

            <div class="text-center mt-4">
                <input type="hidden" name="uid" value="{{ $qrcodeId ?? '' }}">
                <input type="hidden" name="serial_number" value="{{ $serialNumber ?? '' }}">
                <input type="hidden" name="coupon_code" value="{{ $couponCode ?? '' }}">
                <button type="submit" class="bg-color" @if($data['status'] == 'failed') disabled @endif>Redeem Now</button>
                <p class="mt-2">Don't have account? <a class="link" href="{{ route('sign_up',['uid' => $qrcodeId ?? '','serial_number' => $serialNumber ?? '','coupon_code' => $couponCode ?? '']) }}"> SIGN UP</a></p>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
@if($data['status'] == 'failed')
@include('components.message', ['message' => $data['message'],'option'=>config('constants.error.option'),'title'=>config('constants.error.title')])
@endif
@if(\Session::get('status') == 'failed')
@include('components.message', ['message' => \Session::get('message'),'option'=>config('constants.error.option'),'title'=>config('constants.error.title')])
@endif
@if(\Session::get('status') == 'success')
@include('components.message', ['message' => \Session::get('message'),'option'=>config('constants.success.option'),'title'=>config('constants.success.title')])
@endif
@endsection
