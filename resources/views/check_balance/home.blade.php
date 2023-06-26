@extends('main')

@section('title', 'history')

@section('style')
<style type="text/css">
    .item {
        display: flex;
        flex-direction: column;
        justify-content: center;
        background-color: #f7bd11;
        height: 170px;
        width: 170px;
        border: 1px solid black;
        border-radius: 10px;
        margin-top: 20px;
        text-align: center;
        vertical-align: middle;
        font-weight: bold;
    }

    .card_content {
        margin: auto;
    }

    .card {
        margin: 15px;

    }

    .details .serial_no{
        margin-bottom: 0px;
        font-size: 13px;
    }
    .badge{
        font-size: 10px !important;
        padding: 2px 5px !important;
        line-height: 1.1 !important;
    }
    .date {
        font-size: 12px;
        font-weight: 500;
    }

    .card_content {
        min-height: 360px;
        overflow: scroll;
        overflow-x: hidden;
    }

    .card_content::-webkit-scrollbar {
        width: 1px;
    }

    .card_content::-webkit-scrollbar-thumb {
        background-color: #8A0202;
        border-radius: 0px;
    }

    @media screen and (max-width: 768px) {
        .item {
            background-color: #f7bd11;
            height: 130px;
            width: 130px;
            border: 1px solid black;
            border-radius: 10px;
            margin-top: 20px;
            text-align: center;
            vertical-align: middle;
        }
        .card_content{
            min-height:60%;
        }
    }
    b.title{
        font-size: 12px;
    }
</style>

@section('content')
<div class="bg couponCodebg">
    <div class="d-flex flex-row justify-content-around bd-highlight mb-3 container" id="totalEarning">
        <div class="p-2 bd-highlight item">
            <div class="earning">Total Earnings</div>

            <div>{{$data['totalEarnings']}}</div>
        </div>
        <div class="p-2 bd-highlight item">
            <div class="earning ">Pending Amount</div>
            <div class="">{{ $data['pending'] }}</div>
        </div>
    </div>
    <div class="card_content scroll" id="historyRecord">
        @forelse($data['loginHistories'] as $loginHistory)
        <div class="card">
            <div class="card-body d-flex justify-content-between align-middle align-items-center">
                <div class="details">
                    <h6 style="font-weight:bold;" class="serial_no">{{ $loginHistory->qRcodeItem->serial_number }}</h6>

                    @php
                    $statusSpan = '';
                    $processedAt = null;
                    @endphp

                    @foreach($data['payouts'] as $payout)
                        @if($payout->login_history_id == $loginHistory->id)
                            @php
                            $status = $payout->status;
                            if ($status === 0) {
                                $statusSpan = '<span class="badge bg-danger">Failed</span>';
                            } elseif ($status === 1) {
                                $statusSpan = '<span class="badge bg-success">Success</span>';
                            } else {
                                $statusSpan = '<span class="badge bg-warning text-dark">Processing</span>';
                            }

                            if ($status == 1) {
                                $processedAt = "<b class='title'>Processed At : </b><span class='date'>{$loginHistory->created_at->format('d-m-Y h:i:s A')}</span>";
                            }elseif($status == 0){
                                $processedAt = "<b class='title'>Reason : </b><span class='date'>{$payout->reason}</span>";
                            }elseif($status == 2){
                                $processedAt = "<b class='title'>Reason : </b><span class='date'>{$payout->reason}</span>";
                            }
                            @endphp
                        @endif
                    @endforeach

                    <b class="title">Payment Status :</b>
                    @if($statusSpan == '')
                        <span class="badge bg-info">Pending</span>
                    @else
                        {!! $statusSpan !!}
                    @endif

                    <br>

                    @if(!is_null($processedAt))
                    {!! $processedAt !!}
                    @else
                    <b class="title">Reason :</b>
                    <span class="date">
                        Not yet Processed
                    </span>
                    @endif

                </div>
                <div class="amount">
                    ₹ {{ $loginHistory->qRcodeItem->rewardItem->value }}
                </div>
            </div>
        </div>
        @empty
        <i>No Records found</i>
        @endforelse
        <div class="px-2 pb-3">
            {{ $data['loginHistories']->links() }}
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // $( document ).ready(function(){

    var screenHt = window.innerHeight;
    var topContainer = document.getElementById("totalEarning").offsetHeight;
    console.log(screenHt);
    console.log(topContainer);

    var divht = (screenHt - topContainer) - 20;
    console.log(divht);
    document.getElementById("historyRecord").style.height = divht;
    // });
</script>
@endsection
