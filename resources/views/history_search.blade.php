@extends('main')

@section('title', 'history_search')

@section('content')
<div class="bg search_historybg">
    <div class="content">
        <div class="history_search_heading">
            <h1 class="head-title text-center">HELLO!</h1>
            <p class="mb-0 text-center history_search_para">Enter your mobile number </p>
        </div>
        <form class="form-sec history_search_form" action="history_search_mobile" method="POST">
            @csrf
            <!-- <div class="mb-2">
                <input type="text" class="form-input" value="" id="username" placeholder="Enter Name" name="name" required>


            </div> -->
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

            <div class="text-center mt-4">

                <button type="submit" class="bg-color">Submit</button>

            </div>
        </form>
    </div>
</div>
@endsection

@section('script')

@if(\Session::get('status') == 'failed')
@include('components.message', ['message' => \Session::get('message'),'option'=>config('constants.error.option'),'title'=>config('constants.error.title')])
@endif
@endsection
