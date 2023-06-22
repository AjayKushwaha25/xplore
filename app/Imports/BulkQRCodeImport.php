<?php

namespace App\Imports;

use Throwable;
use App\Models\{QRCodeItem};
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\{ToModel,ToCollection, Importable, WithStartRow, WithValidation};
use DB;

class BulkQRCodeImport implements ToCollection, WithValidation, WithStartRow
{
    use Importable;

    protected $rows = 0;

    public function collection(Collection $rows)
    {
        // dd($rows);
        foreach ($rows as $row)
        {
            $qrCode = QRCodeItem::whereSerialNumber($row[0])->first();
            if($qrCode){
                $qrCode->update([
                    'key' => $row[1],
                ]);
            }
        }
    }

    public function rules():array
    {
        return [
            '*.0' => ['required'],
            '*.1' => ['required'],
        ];
    }
    public function customValidationAttributes()
    {
        return [
            '0' => 'Serial Number',
            '1' => 'Key',
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
}
