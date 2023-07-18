<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection,WithCustomStartCell, WithMapping, WithHeadings, WithEvents, WithStyles};
// use Maatwebsite\Excel\Concerns\;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterReportExport implements FromCollection, WithCustomStartCell, WithMapping, WithHeadings, WithEvents, WithStyles
{
    public function dateRange($startDate,$endDate){
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
    {
        return[
            'Location',
            'WD Code',
            'Total Coupon Shared',
            'Total Coupon Utilised',
            'Total Unique Retailers (till Date)',
            '> 2 transactions',
            '> 5 transactions',
        ];
    }

    public function map($loginHistory): array
    {
        // dd($loginHistory);
        return [
            $loginHistory['city'],
            $loginHistory['code'],
            $loginHistory['total'],
            $loginHistory['redeemed'],
            $loginHistory['totalRetailers'],
            '',
            '',
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

                /* Merge Column */
                $event->sheet->mergeCells('F1:G1');
                $event->sheet->setCellValue('F1', "Retailer Count till Date");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $event->sheet->getDelegate()->getStyle('A1:G1')->applyFromArray($styleArray);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A2:G2')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $totalQRCode = \App\Models\QRCodeItem::with([
            'wd:id,code,city_id',
            'wd.city:id,name'
        ])
            ->whereBetween('created_at',[$this->startDate, $this->endDate])
            ->select('id', 'wd_id', 'is_redeemed')
            ->get()
            ->groupBy(function ($item) {
                return $item['wd']['code'];
            })
            ->map(function ($group) {
                $redeemed = $group->where('is_redeemed', 1)->count();
                $cityName = $group->first()['wd']['city']['name'];
                return [
                    'city' => $cityName,
                    'total' => count($group),
                    'redeemed' => $redeemed
                ];
            })
            ->toArray();

        $data = \App\Models\LoginHistory::join('q_r_code_items', 'login_histories.q_r_code_item_id', '=', 'q_r_code_items.id')
            ->join('wd', 'q_r_code_items.wd_id', '=', 'wd.id')
            ->join('cities', 'wd.city_id', '=', 'cities.id');
            ->whereBetween('created_at',[$this->startDate, $this->endDate])
            ->select(
                'wd.code',
                'cities.name AS city',
                \DB::raw('COUNT(DISTINCT login_histories.retailer_id) AS totalRetailers'),
                \DB::raw('SUM(q_r_code_items.is_redeemed) AS redeemed')
            )
            ->groupBy('q_r_code_items.wd_id', 'wd.code', 'cities.name')
            ->get()
            ->toArray();

        // Merge the two queries
        $mergedData = [];
        foreach ($data as $item) {
            $code = $item['code'];
            if (isset($totalQRCode[$code])) {
                $mergedData[] = array_merge($item, $totalQRCode[$code]);
            }
        }

        return collect($mergedData);
    }

    public function startCell(): string
    {
        return 'A2';
    }
}
