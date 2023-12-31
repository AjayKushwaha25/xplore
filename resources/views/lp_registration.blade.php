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
            <p class="text-center">Register</p>
        </div>
        <form class="form-sec" action="{{ route('lp_retailer.store') }}" method="POST">
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

            <div class="text-center mt-4">
                <button type="submit" class="bg-color">GENERATE CODE</button>
            </div>
        </form>
    </div>
</div>

