<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\CouponCodeHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CouponCodeHistoryExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return[
            'Lp Retailer Id',
            'Coupon code id',
            'Created At',
            
        ];
    }

    public function map($coupon_code_history): array
    {
        return [
            $coupon_code_history->lp_retailer_id,
            $coupon_code_history->coupon_code_id,
            $coupon_code_history->created_at,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('C')->setAutoSize(true);
          },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return CouponCodeHistory::whereBetween('created_at',[$this->startDate, $this->endDate])
        ->orderBy('created_at','DESC')
        ->get();
    }
}
