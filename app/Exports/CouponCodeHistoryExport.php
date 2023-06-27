<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\{CouponCodeHistory,LpRetailer,CouponCode};
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
            // 'Lp Retailer Id',
            // 'Coupon code id',
            // 'Created At',

            'Name',
            'Mobile number',
            'Whatsapp number',
            'UPI ID',
            'Serial Number',
            'Reward Value',
            'Scanned At',
            
        ];
    }

    public function map($coupon_code_history): array
    {
        return [
            $coupon_code_history->lpRetailer->name,
            $coupon_code_history->lpRetailer->mobile_number,
            $coupon_code_history->lpRetailer->whatsapp_number,
            $coupon_code_history->lpRetailer->upi_id,
            $coupon_code_history->couponCode->code,
            $coupon_code_history->couponCode->rewardItem->value, 
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
                $event->sheet->getDelegate()->getColumnDimension('D')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('E')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('F')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
          },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // $couponCodeHistoryData = CouponCodeHistory::whereBetween('created_at',[$this->startDate, $this->endDate])
        // ->orderBy('created_at','DESC')
        // ->get();

        return  CouponCodeHistory::query()
                            ->with([
                                'lpRetailer:id,name,mobile_number,whatsapp_number,upi_id',
                                'couponCode:id,code,reward_item_id',
                                'couponCode.rewardItem:id,value'
                                // 'qRCodeItem.wd:id,code'
                            ])
                            ->select('lp_retailer_id','coupon_code_id','created_at')
                            ->get();
    }
}
