@extends('main')

@section('title', 'Thank You')

@section('style')
<link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />
<style>
    .coin-img{
        width: 100px;
    }
    .bg-image{
        background-image: url();
    }
    .content .head-title{
/*        margin-top: 30%;*/
    }
    h3{
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="bg couponRewardbg">
    <div class="content">
        @if(\Session::get('status')=='success')
        <h1 class="head-title animate__animated animate__bounceIn">THANK YOU !</h1>
        <img class="img-fluid coin-img animate__animated animate__bounceInDown" src="{{ asset('images/icons/'.\Session::get('data')['img_path']) }}">
        <h3 class="head-title animate__animated animate__bounceInUp">{{ \Session::get('message') }}</h3>
        @else
        <script type="text/javascript">
            window.location.href = "{{ route('coupon.index') }}";
        </script>
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection
