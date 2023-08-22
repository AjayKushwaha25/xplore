<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItcReportExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return[
            'RazorpayX Account Number',
            'Payout Amount (in Rupees)',
            'Payout Currency',
            'Payout Mode',
            'Payout Purpose',
            'Fund Account Id',
            'Fund Account Type',
            'Fund Account Name',
            'Fund Account Ifsc',
            'Fund Account Number',
            'Fund Account Vpa',
            'Fund Account Phone Number',
            'Contact Name',
            'Payout Narration',
            'Payout Reference Id',
            'Fund Account Email',
            'Contact Type',
            'Contact Email',
            'Contact Mobile',
            'Contact Reference Id',
            'notes[place]',
            'notes[code]',
        ];
    }

    public function map($loginHistory): array
    {
        return [
            4564561483493465,
            $loginHistory->qRCodeItem->rewardItem->value,
            'INR',
            'UPI',
            'cashback',
            null,
            'vpa',
            NULL,
            NULL,
            NULL,
            $loginHistory->retailer->upi_id,
            NULL,
            $loginHistory->retailer->name,
            NULL,
            NULL,
            NULL,
            'vendor',
            NULL,
            $loginHistory->retailer->mobile_number,
            NULL,
            NULL,
            "{$loginHistory->qRCodeItem->wd->code}:{$loginHistory->retailer->upi_id}:{$loginHistory->retailer->mobile_number}:{$loginHistory->qRCodeItem->serial_number}:{$loginHistory->qRCodeItem->rewardItem->value}:\"{$loginHistory->created_at}\":xplore",
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
                $event->sheet->getDelegate()->getColumnDimension('J')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('K')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('L')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('M')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('N')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('O')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('P')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('Q')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('R')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('S')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('T')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('U')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('V')->setAutoSize(true);
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