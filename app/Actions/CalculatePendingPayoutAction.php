<?php

namespace App\Actions;

use App\Models\{LoginHistory, Payout};

class CalculatePendingPayoutAction
{
    public function handle($retailerId, $is_dashboard = false): array
    {
        $loginHistory = LoginHistory::with([
                'qRCodeItem:id,reward_item_id,serial_number',
                'qRCodeItem.rewardItem:id,value'
            ])
            ->whereRetailerId($retailerId)
            ->latest()
            ->select('id', 'q_r_code_item_id','created_at');

        $payouts = Payout::with('loginHistory.qRCodeItem.rewardItem:id,value')
            ->whereIn('login_history_id', $loginHistory->pluck('id'))
            ->select(['id', 'login_history_id', 'utr', 'status', 'reason', 'processed_at'])
            ->get();

        $totalEarnings = $loginHistory->get()->sum(function ($history) {
            return $history->qRCodeItem->rewardItem->value;
        });

        $successfulPayout = $payouts->filter(function ($payout) {
            return $payout->status == 1;
        })->sum(function ($payout) {
            return $payout->loginHistory->qRCodeItem->rewardItem->value;
        });

        $pendings = $totalEarnings - $successfulPayout;

        $data = [
            'totalEarnings' => $totalEarnings,
            'pending' => $pendings,
            'loginHistories' => $is_dashboard ? $loginHistory->get() : $loginHistory->paginate(10),
            'loginHistoryCount' => $loginHistory->count(),
            'payouts' => $payouts,
            'successfulPayout' => $successfulPayout,
        ];
        return $data;
    }
}
