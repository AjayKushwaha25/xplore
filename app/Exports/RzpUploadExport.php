<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RzpUploadExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return[
            "Beneficiary Name (Mandatory) Special characters not supported",
            "Beneficiary's UPI ID (Mandatory)",
            "Payout Amount (Mandatory) Amount should be in rupees",
            "Payout Narration (Optional) Will appear on bank statement (max 30 char with no special characters)",
            "Notes (Optional) A note for internal reference",
            "Phone Number (Optional)",
            "Email ID (Optional)",
            "Contact Reference ID (Optional) Eg: Employee ID or Customer ID",
            "Payout Reference ID (Optional) Eg: Bill no or Invoice No or Pay ID",
        ];
    }

    public function map($loginHistory): array
    {
        return [
            $loginHistory->retailer->name,
            $loginHistory->retailer->upi_id,
            $loginHistory->qRCodeItem->rewardItem->value,
            NULL,
            "{$loginHistory->qRCodeItem->wd->code}:{$loginHistory->retailer->upi_id}:{$loginHistory->retailer->mobile_number}:{$loginHistory->qRCodeItem->serial_number}:{$loginHistory->qRCodeItem->rewardItem->value}:\"{$loginHistory->created_at}\":xplore",
            NULL,
            NULL,
            NULL,
            NULL,
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
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:v1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return LoginHistory::with(['retailer:id,name,mobile_number,upi_id,whatsapp_number','qRCodeItem:id,serial_number,reward_item_id,wd_id','qRCodeItem.wd:id,code','qRCodeItem.rewardItem:id,value'])
                    ->whereBetween('created_at',[$this->startDate, $this->endDate])
                    ->select('id','retailer_id','q_r_code_item_id','created_at')
                    ->orderBy('created_at','ASC')
                    ->get();
    }

}
