<style type="text/css">
    form{
        height: 100svh;
        display: flex;
        flex-direction: column;
        gap: 50px;
        justify-content: center;
        align-items: center;
    }
    input {
        width: 90%;
        font-size: 60px;
    }
    p{
        font-size:60px;
        margin: 0 !important;
    }
    .alert{
        width: 80%;
        padding: 20px 50px;
        color: #fff;
        text-align: center;
    }
    .alert-success{
        background-color: green;
    }
    .alert-danger{
        background-color: red;
    }
</style>
<form action="{{ route('update.key') }}" method="POST">
    @if (\Session::has('success'))
    <div class="alert alert-success">
        <p style="margin-bottom: 0;">{!! \Session::get('success') !!}</p>
    </div>
    @endif
    @if (\Session::has('failed'))
    <div class="alert alert-danger">
        <p style="margin-bottom: 0;">{!! \Session::get('failed') !!}</p>
    </div>
    @endif
    @csrf
    <input type="text" name="key" value="{{ request()->get('k') }}" readonly required>
    <input type="text" name="serial_number" value="{{ session()->has('sr_no') ? session('sr_no'): '' }}" placeholder="Serial Number" required>
    <input type="submit" value="Submit">
</form>
