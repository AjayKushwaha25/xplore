<?php

namespace App\Imports;

use Throwable;
use App\Models\{QRCodeItem,Retailer,Payout, LoginHistory};
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\{ToModel,ToCollection, Importable, WithStartRow, WithValidation};
use DB;

class PayoutImport implements ToCollection, WithValidation, WithStartRow
{
    use Importable;

    protected $rows = 0;

    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row)
        {
            $qrCodeId = QRCodeItem::whereSerialNumber($row[0])->value('id');
            $retailerId = Retailer::whereMobileNumber($row[1])->value('id');


            if(!$qrCodeId){
                throw new \Exception('Invalid Serial Number.');
            }
            if(!$retailerId){
                throw new \Exception('Invalid Retailer.');
            }

            $loginHistoryId = LoginHistory::where(['q_r_code_item_id'=>$qrCodeId, 'retailer_id' => $retailerId])->value('id');

            if(!$loginHistoryId){
                // throw new \Exception("Login history for this entry does not exists for QR code {$row[0]} and Retailer {$row[1]}");
                \Log::error("Login history for this entry does not exists for QR code {$row[0]} and Retailer {$row[1]}");
            } else {
                $processedDate = NULL;
                if($row[5]){
                    $date = Carbon::createFromFormat('d-m-Y H:i', $row[5]);
                    $processedDate = $date->format('Y-m-d H:i:s');
                }

                $payouts = Payout::updateOrCreate([
                                    'login_history_id' => $loginHistoryId,
                                ],[
                                    'utr' => $row[2] ?? '',
                                    'status' => $this->getStatusAsBool(\Str::lower($row[3])),
                                    'reason' => $row[4] ?? '',
                                    'processed_at' => $processedDate,
                                ]);
            }
        }
    }

    public function rules():array
    {
        return [
            '*.0' => ['required'],
            '*.1' => ['required'],
            '*.2' => ['sometimes'],
            '*.3' => ['required'],
            '*.4' => ['sometimes'],
            '*.5' => ['sometimes'],
        ];
    }
    public function customValidationAttributes()
    {
        return [
            '0' => 'Serial Number',
            '1' => 'Mobile Number',
            '2' => 'UTR',
            '3' => 'Status',
            '4' => 'Reason',
            '5' => 'Processed At',
        ];
    }
    public function startRow(): int
    {
        return 2;
    }
    public function getRowCount(): int
    {
        return $this->rows;
    }


    public function getStatusAsBool($value)
    {
        $statusBool = 0;
        switch ($value) {
            case 'processed':
                $statusBool = 1;
                break;
            case 'processing':
                $statusBool = 2;
                break;
            case 'reversed':
                $statusBool = 0;
                break;
            case 'success':
                $statusBool = 1;
                break;
            case 'failed':
                $statusBool = 0;
                break;
            default:
                $statusBool = 0;
                break;
        }
        return $statusBool;
    }
}
