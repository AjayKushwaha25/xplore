@extends('main')

@section('title', 'Reward')

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
        margin-top: 30%;
    }
</style>
@endsection

@section('content')
<div class="bg rewardbg">
    <div class="content">
        @if(\Session::get('status')=='success')
        <h1 class="head-title animate__animated animate__bounceIn">YOU WON</h1>
        <img class="img-fluid coin-img animate__animated animate__bounceInDown" src="{{ asset('images/icons/'.\Session::get('data')['img_path']) }}">
        <p class="cash-text animate__animated animate__bounceInUp">Cashback</p>
        @else
        <script type="text/javascript">
            window.location.href = "{{ route('logout') }}";
        </script>
        @endif
    </div>
</div>
@endsection

@section('script')

@endsection
