<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LoginHistoryExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function data($startDate,$endDate,$region){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->region = $region;
    }

    public function headings(): array
    {
        return[
            'Name',
            'Mobile number',
            'Whatsapp number',
            'UPI ID',
            'WD Code',
            'City',
            'Serial Number',
            'Reward Value',
            'IP Address',
            'Scanned At',
        ];
    }

    public function map($loginHistory): array
    {
        return [
            $loginHistory->retailer->name,
            $loginHistory->retailer->mobile_number,
            $loginHistory->retailer->whatsapp_number,
            $loginHistory->retailer->upi_id,
            $loginHistory->qRCodeItem->wd->code,
            $loginHistory->qRCodeItem->wd->city->name,
            $loginHistory->qRCodeItem->serial_number,
            $loginHistory->qRCodeItem->rewardItem->value,
            $loginHistory->ip_address,
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
                $event->sheet->getDelegate()->getColumnDimension('G')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('J')->setAutoSize(true);

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $region = $this->region;
        return  LoginHistory::query()
                            ->with([
                                'retailer:id,name,mobile_number,whatsapp_number,upi_id',
                                'qRCodeItem:id,serial_number,reward_item_id,wd_id',
                                'qRCodeItem.rewardItem:id,value',
                                'qRCodeItem.wd:id,code,city_id',
                                'qRCodeItem.wd.city:id,name'
                            ])
                            ->when($region, function($query) use ($region){
                                $query->whereHas('qRCodeItem.wd.city',function ($query) use ($region){
                                $query->where('region_id', $region);
                            });

                        })
                            ->select('id','ip_address','retailer_id','q_r_code_item_id','created_at')
                            ->get();
    }
}
