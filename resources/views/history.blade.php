@extends('main')

@section('title', 'history')

@section('style')
<style type="text/css">
.item {
    background-color: #f7bd11;
    height: 170px;
    width: 170px;
    border: 1px solid black;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 50px;
    text-align: center;
    vertical-align: middle;
    font-weight:bold;
    
}
.card_content{
    margin:auto;
}
.card {
    margin: 15px;
    /* line-height: 30px; */
    font-size:15px;
    font-weight:bold;
    /* border-radius: 0.3rem; */
    border-radius: 40px;
    box-shadow:4px 4px 10px #d4cdc3;
    /* width:80%; */
}
.earning{
    margin-top:50px;
}
.amount{
    color:green;
    font-size:15px;
    font-weight:900;
    /* margin:auto; */
    margin-top:auto;
    margin-bottom:auto;
    

}
.date{

    font-size:12px;
    font-weight:500;
}
.card-body{
    
}
@media screen and (max-width: 320px) {
    .item {
    background-color: #f7bd11;
    height: 130px;
    width: 130px;
    border: 1px solid black;
    border-radius: 10px;
    margin-top: 20px;
    margin-bottom: 40px;
    text-align: center;
    vertical-align: middle;
}
.earning{
    margin-top:30px;
}
}
</style>

@section('content')
<div class="bg search_historybg">
    <div class="">
        <div class="d-flex flex-row justify-content-around bd-highlight mb-3 container">
            <div class="p-2 bd-highlight item">
                <div class="earning">Total Earning</div>
                <div>300</div>
            </div>
            <div class="p-2 bd-highlight item">
                <div class="earning ">Pending</div>
                <div class="">20</div>
            </div>
        </div>
        <div class="card_content">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-middle">
                <div class="serial_no">
                    AA1<br>
                    <span class="date">19-04-2023</span>
                </div>
                <div class="amount align-middle">
                    +20
                </div>

            </div>
        </div>
        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <div class="serial_no">
                    AA1<br>
                    <span class="date">19-04-2023</span>
                </div>
                <div class="amount">
                    +20
                </div>

            </div>
        </div>
        
        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <div class="serial_no">
                    AA1<br>
                    <span class="date">19-04-2023</span>
                </div>
                <div class="amount">
                    +20
                </div>

            </div>
        </div>
</div>
    </div>
</div>
@endsection
