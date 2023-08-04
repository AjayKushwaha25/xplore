<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PayoutExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function data($startDate,$endDate,$region){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->region = $region;
        // dd($this->region);
    }

    public function headings(): array
    {
        return[
            'Amount',
            'Username',
            'Mobile number',
            'WD Code',
            'City',
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
            $loginHistory->qRCodeItem->wd->code,
            $loginHistory->qRCodeItem->wd->city->name,
            $loginHistory->retailer->upi_id,
            $loginHistory->qRCodeItem->serial_number,
            config('app.name').":{$loginHistory->qRCodeItem->wd->code}:{$loginHistory->retailer->upi_id}:{$loginHistory->retailer->mobile_number}:{$loginHistory->qRCodeItem->serial_number}:\"{$loginHistory->created_at}\"",
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

            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $region = $this->region;
         return  LoginHistory::query()->
                            with([
                                'retailer:id,name,mobile_number,upi_id,whatsapp_number',
                                'qRCodeItem:id,serial_number,reward_item_id,wd_id',
                                'qRCodeItem.rewardItem:id,value',
                                'qRCodeItem.wd:id,code,city_id',
                                'qRCodeItem.wd.city:id,name,region_id',
                            ])
                            ->whereHas('qRCodeItem.wd.city',function ($query) use ($region){
                                $query->where('region_id', $region);
                            })
                            ->whereBetween('created_at',[$this->startDate, $this->endDate])
                            ->select('id','retailer_id','q_r_code_item_id','created_at')
                            ->orderBy('created_at','ASC')
                            ->get();

                            // dd($loginHistory);
    }

}
