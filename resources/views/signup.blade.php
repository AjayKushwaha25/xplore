@extends('main')
@section('title', 'Signup')
@section('style')
<style type="text/css">
    .content .head-title{
        margin-top: 100%;
    }
</style>
@endsection

@section('content')
<div class="bg couponCodebg">
    <div class="content">
        <div>
            <h1 class="head-title text-center">HELLO!</h1>
            <p class="text-center">Create account</p>
        </div>
        <form class="form-sec" action="{{ route('check.register') }}" method="POST">
            @csrf
            <div class="mb-2">
                <input type="text" class="form-input @error('name') is-invalid @enderror" value="{{ old('name') }}" id="name" placeholder="Enter Name" name="name" required>
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
                <input type="tel" class="form-input @error('mobile_number') is-invalid @enderror" value="{{ old('mobile_number') }}" id="mobile_number" placeholder="Phone No." name="mobile_number" required>
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
                <input type="tel" class="form-input @error('whatsapp_number') is-invalid @enderror" value="{{ old('whatsapp_number') }}" id="whatsapp_number" placeholder="Whatsapp No." name="whatsapp_number" required>
                @error('whatsapp_number')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>

            <div class="mb-2">
                <input type="radio" id="upi_id_btn" name="payment_mode" value="upi_id" {{ old('payment_mode') == 'upi_id' ? 'checked' : 'checked' }} required  />
                <label class="text-white" for="upi_id_btn">UPI ID</label>

                <input type="radio" id="paytm_number_btn" name="payment_mode" value="paytm_number" {{ old('payment_mode') == 'paytm_number' ? 'checked' : '' }}/>
                <label class="text-white" for="paytm_number_btn">Paytm Number</label>

                @error('upi_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>

            <div class="mb-2">
                <input type="text" class="form-input @error('upi_id') is-invalid @enderror" value="{{ old('upi_id') }}" id="upi_id" placeholder="UPI ID" name="upi_id">
                @error('upi_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div>

            {{-- <div class="mb-2" id="paytmNumberDiv" style="display: none;">
                <input type="text" class="form-input @error('upi_id') is-invalid @enderror" value="{{ old('upi_id') }}" id="paytm_number" placeholder="Paytm Number" name="upi_id">
                @error('upi_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                <script type="text/javascript">
                    toastr.error("{{ $message }}", 'Error!',{"positionClass": "toast-top-right"})
                </script>
                @enderror
            </div> --}}

            <div class="mb-2">
                <input type="number" class="form-input @error('coupon_code') is-invalid @enderror" value="{{ old('coupon_code') }}" id="coupon_code" placeholder="Enter coupon code" name="coupon_code" required>
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
                <button type="submit" class="bg-color" @if($data['status'] == 'failed') disabled @endif>SIGN UP</button>
                <p class="mt-1">Already have account? <a class="link" href="{{ route('login',['uid' => $qrcodeId ?? '','serial_number' => $serialNumber ?? '','coupon_code' => $couponCode ?? '']) }}"> LOGIN</a></p>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')

<script src="{{ asset('js/jquery-3.6.4.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var radioButtons = $('input[name="payment_mode"]');
        var selectedPaymentMode = $('input[name="payment_mode"]:checked');
        var upiID = $("#upi_id");

        if (selectedPaymentMode.val() === "upi_id") {
            upiID.attr('type', 'text');
            upiID.attr('placeholder','UPI ID');
        } else if (selectedPaymentMode.val() === "paytm_number") {
            upiID.attr('type', 'number');
            upiID.attr('placeholder', 'Paytm Number');
        }

        // Add event listeners to the radio buttons
        radioButtons.each(function() {
            $(this).on('change', function() {
                upiID.val('');
                if (this.value === "upi_id") {
                    upiID.attr('type', 'text');
                    upiID.attr('placeholder','UPI ID');
                } else if (this.value === "paytm_number") {
                    upiID.attr('type','number');
                    upiID.attr('placeholder','Paytm Number');
                }
            });
        });

        function upiValid(inputs, inputsID, inputsErr){
            userInput = inputs;
            showError = inputsErr;
            var regex = new RegExp('^([a-zA-Z0-9]+)([\.{1}])?([a-zA-Z0-9]+)\@?(paytm|okicici|oksbi|okaxis|okhdfcbank|ybl|upi|axl)+$');
            if(!regex.test(userInput)){
                showError.show();
                inputsID.focus();
                inputsID.css('border-color','#dc3545');
                return false;
            }
            showError.hide();
            inputsID.css('border-color','#ced4da');
            return true;
        }
    });
</script>
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
