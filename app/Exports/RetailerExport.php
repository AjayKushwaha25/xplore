<?php

namespace App\Exports;

use App\Models\Retailer;
use Maatwebsite\Excel\Concerns\{FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RetailerExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithStyles
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
            'Registered At',
            'City',
            'Region',
        ];
    }

    public function map($retailer): array
    {
        return [
            $retailer->name,
            $retailer->mobile_number,
            $retailer->whatsapp_number,
            $retailer->upi_id,
            $retailer->created_at,
            $retailer->loginHistories[0]->qRCodeItem->wd->city->name,
            $retailer->loginHistories[0]->qRCodeItem->wd->city->region->region,

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
        // return Retailer::whereBetween('created_at',[$this->startDate, $this->endDate])
        //             ->orderBy('created_at','DESC')
        //             ->get();
        // return Retailer::all();

        $region = $this->region;
        $retailer = Retailer::query()
        ->with([
            'loginHistories:id,q_r_code_item_id,retailer_id',
            'loginHistories.qRCodeItem:id,wd_id',
            'loginHistories.qRCodeItem.wd:id,city_id',
            'loginHistories.qRCodeItem.wd.city:id,region_id,name',
            'loginHistories.qRCodeItem.wd.city.region:id,region',
        ])
        ->when($region, function($query) use ($region){
            $query->whereHas('loginHistories.qRCodeItem.wd.city',function ($query) use ($region){
                $query->where('region_id', $region);
            });
        })
        ->get(['id','name','mobile_number','whatsapp_number','upi_id','created_at']);

        // dd($retailer);
        return $retailer;

                // dd($retailer);
    }
}
