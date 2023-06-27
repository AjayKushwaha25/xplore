<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\LpRetailer;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class LpRetailerExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{

    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
   
    public function headings(): array
    {
        return[
            'Name',
            'Mobile number',
            'Whatsapp number',
            'UPI ID',
            'Registered At',
        ];
    }

    public function map($lp_retailer): array
    {
        return [
            $lp_retailer->name,
            $lp_retailer->mobile_number,
            $lp_retailer->whatsapp_number,
            $lp_retailer->upi_id,
            $lp_retailer->created_at
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

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }

    public function collection()
    {
        return LpRetailer::whereBetween('created_at',[$this->startDate, $this->endDate])
        ->orderBy('created_at','DESC')
        ->get();
    }
}
