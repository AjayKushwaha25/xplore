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
    .badge {
    font-size: 10px !important;
    padding: 2px 5px !important;
    line-height: 1.1 !important;
    font-weight: bold !important;
    font-size: 13px;
    padding-top: 5px;
    background-color: #14B2AC;
    color: white;
    padding: 4px 8px;
    text-align: center;
    border-radius: 5px;
    margin-top: 7px;
}

svg {
    vertical-align: middle;
    height: 30px;
}

.modal_amount {
    text-align: center;
    font-size: 35px;
    font-weight: 700;
}
.modal_payout_date{
    text-align:center;
    padding:5px;
    font-size: 13px;
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
.modal_line_breaker{
    border-top:1px solid #615e5e;
    margin:auto;
    width:50%;
}
.modal_upi_id,.modal_utr_no, .modal_scanned_date, .modal_brand, .modal_wd_code, .modal_payment_status, .modal_reason{
    font-size:11px;
    margin-top:10px;
    margin-left:15px;
    margin-bottom:5px;
    font-weight:700;
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
        <div class="card" data-bs-toggle="modal" data-bs-target="#exampleModal" data-login-history-id="{{ $loginHistory->id }}">
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Login History Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="modal_amount" id="amount"><svg xmlns="http://www.w3.org/2000/svg" height="1em"
                                    viewBox="0 0 320 512">
                                    <!--! Font Awesome Free 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                                    <path
                                        d="M0 64C0 46.3 14.3 32 32 32H96h16H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H231.8c9.6 14.4 16.7 30.6 20.7 48H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H252.4c-13.2 58.3-61.9 103.2-122.2 110.9L274.6 422c14.4 10.3 17.7 30.3 7.4 44.6s-30.3 17.7-44.6 7.4L13.4 314C2.1 306-2.7 291.5 1.5 278.2S18.1 256 32 256h80c32.8 0 61-19.7 73.3-48H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H185.3C173 115.7 144.8 96 112 96H96 32C14.3 96 0 81.7 0 64z" />
                                </svg>10</div>
                            </div>
                            <div class="modal_line_breaker"></div>
                            <div class="modal_payout_date" id ="payout_date">June 17,2023</div>
                            <div class = "modal_upi_id">
                                <div><b>UPI ID</b></div>
                                <div id="upi"></div>
                            </div>
                            <div class = "modal_utr_no">
                                <div><b>UTR NO</b></div>
                                <div id="utr"></div>
                            </div>
                            
                            <div class = "modal_scanned_date">
                                <div><b>Scanned Date</b></div>
                                <div id="date"></div>
                            </div>
                            <div class = "modal_wd_code">
                                <div><b>WD Code</b></div>
                                <div id="code"></div>
                            </div>
                            <div class = "modal_payment_status">
                                <div><b>Payment Status</b></div>
                                <div id="status"></div>
                            </div>
                            <div class = "modal_reason">
                                <div><b>Reason</b></div>
                                <div id="reason"></div>
                                <br>
                            </div>
                        <div class="modal-footer">
                          </div>
                    </div>
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

    $(".card").on('click', function() {
        // console.log('click')
        // e.preventDefault()
        lHID = $(this).data('login-history-id')
        console.log(lHID);
        
        if (lHID) {
            $.ajax({
                url: "{{ route('history_modal') }}",
                type: 'POST',
                data: {
                    lH_id: lHID,
                    '_token': "{{ csrf_token() }}"

                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        // console.log(response.data);
                        $loginHistoryData = response.data;
                        // console.log($loginHistoryData);
                       
                        var text = $loginHistoryData.created_at;
                        let date = text.slice(0,10);

             
                        $('#date').html(date);
                        $('#code').html($loginHistoryData.q_r_code_item.serial_number);
                       
                        $('#upi').html($loginHistoryData.retailer.upi_id);
                        $('#amount').html($loginHistoryData.q_r_code_item.reward_item.value);

                        $payoutData = response.payouts; 
                        // console.log($payoutData);
                      
                        var status1 = $payoutData.status;
                        console.log(status1);

                        if(status1 == "1"){
                            var status = "success";
                        }
                        else if(status1 == "0"){
                            var status = "failed";
                        }
                        else if(status1 == "2"){
                            var status = "processing";
                        }
                       
                        var p_date = $payoutData.created_at;
                        let payout_date = p_date.slice(0,10);


                       
               
                        if($payoutData != "")
                        {
                            $('#reason').html($payoutData.reason);
                            $('#payout_date').html(payout_date);
                            $('#utr').html($payoutData.utr);
                            $('#status').html(status);
                        }
                        else{
                            $('#reason').html("null");
                            $('#payout_date').html("null");
                            $('#utr').html("null");
                            $('#status').html("null");
                        }
                    }
                }
              
            });


        } else {
            console.log('id not found')
        }

    });

</script>
@endsection
