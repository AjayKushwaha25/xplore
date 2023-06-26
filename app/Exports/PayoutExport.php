<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayoutExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return[
            'Amount',
            'Username',
            'Mobile number',
            'UPI ID',
            'Serial Number',
            'Notes',
            'Scanned At',
        ];
    }

    public function map($loginHistory): array
    {
        return [
            $loginHistory->qRCodeItem->rewardItem->value,
            $loginHistory->retailer->name,
            $loginHistory->retailer->mobile_number,
            // $loginHistory->qRCodeItem->wd->code,
            $loginHistory->retailer->upi_id,
            $loginHistory->qRCodeItem->serial_number,
            "{$loginHistory->retailer->upi_id}:{$loginHistory->retailer->mobile_number}:{$loginHistory->qRCodeItem->serial_number}:\"{$loginHistory->created_at}\"",
            $loginHistory->created_at
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                // $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LoginHistory::with(['retailer:id,name,mobile_number,upi_id,whatsapp_number','qRCodeItem:id,serial_number,reward_item_id','qRCodeItem.rewardItem:id,value'])
                    ->whereBetween('created_at',[$this->startDate, $this->endDate])
                    ->select('id','retailer_id','q_r_code_item_id','created_at')
                    ->orderBy('created_at','ASC')
                    ->get();
    }

}
