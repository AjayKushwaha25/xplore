<?php

namespace App\Exports;

use App\Models\LoginHistory;
use Maatwebsite\Excel\Concerns\{FromCollection,WithCustomStartCell, WithMapping, WithHeadings, WithEvents, WithStyles};
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
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
            '% coupon utilization',
            'Total Unique Retailers',
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
            $loginHistory['total']!==0 ? $loginHistory['total'] : '0',
            $loginHistory['redeemed']!==0 ? $loginHistory['redeemed'] : '0',
            $loginHistory['percentage']!==0 ? $loginHistory['percentage'] : '0',
            $loginHistory['totalRetailers']!==0 ? $loginHistory['totalRetailers'] : '0',
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
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);
                $event->sheet->getDelegate()->getColumnDimension('H')->setAutoSize(true);

                /* Merge Column */
                $event->sheet->mergeCells('G1:H1');
                $event->sheet->setCellValue('G1', "Retailer Count");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray($styleArray);


                $highestColumn = $event->sheet->getHighestColumn();
                $lastRow = $event->sheet->getHighestDataRow();

                // Calculate and insert grand totals
                $event->sheet->setCellValue("A" . ($lastRow + 2), 'Grand Total');
                $event->sheet->setCellValue("C" . ($lastRow + 2), "=SUM(C3:C" . $lastRow . ")");
                $event->sheet->setCellValue("D" . ($lastRow + 2), "=SUM(D3:D" . $lastRow . ")");
                $event->sheet->setCellValue("E" . ($lastRow + 2), "=D" . ($lastRow + 2) . "/C" . ($lastRow + 2));
                $event->sheet->setCellValue("F" . ($lastRow + 2), "=SUM(F3:F" . $lastRow . ")");


                // Set formatting for the grand total row
                $event->sheet->getStyle("A" . ($lastRow + 2) . ":" . $highestColumn . ($lastRow + 2))
                    ->getFont()
                    ->setBold(true);

                $event->sheet->getStyle("D" . ($lastRow + 2) . ":" . $highestColumn . ($lastRow + 2))
                    ->getNumberFormat()
                    ->setFormatCode("#,##0");

                $event->sheet->getStyle("E")
                    ->getNumberFormat()
                    ->setFormatCode("0.00%");

                $event->sheet->getStyle("E" . ($lastRow + 2))
                    ->getNumberFormat()
                    ->setFormatCode("0.00%");
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $wd = \App\Models\WD::with([
            'city:id,name'
        ])
        ->get(['code', 'city_id']);

        $totalQRCode = \App\Models\QRCodeItem::with([
            'wd:id,code,city_id',
            'wd.city:id,name'
        ])
        ->select('id', 'wd_id', 'is_redeemed')
        ->get()
        ->groupBy(function ($item) {
            return $item['wd']['code'];
        })
        ->map(function ($group) {
            $total = count($group);
            $redeemed = $group->where('is_redeemed', 1)->count();
            $cityName = $group->first()['wd']['city']['name'];
            $percentage = $total > 0 ? ($redeemed / $total) : 0; // Calculate percentage
            return [
                'city' => $cityName,
                'total' => $total,
                'redeemed' => $redeemed,
                'percentage' => $percentage
            ];
        })
        ->toArray();


        $data = \App\Models\LoginHistory::join('q_r_code_items', 'login_histories.q_r_code_item_id', '=', 'q_r_code_items.id')
            ->join('wd', 'q_r_code_items.wd_id', '=', 'wd.id')
            ->join('cities', 'wd.city_id', '=', 'cities.id')
            ->whereBetween('login_histories.created_at', [$this->startDate, $this->endDate])
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
        foreach ($wd as $wdItem) {
            $code = $wdItem['code'];
            $mergedItem = [
                'code' => $code,
                'city' => $wdItem['city']['name'] ?? 'Unknown City',
                'total' => '0',
                'redeemed' => '0',
                'percentage' => '0%',
                'totalRetailers' => '0'
            ];

            // Find the matching item in the $data array
            $matchingItem = null;
            foreach ($data as $item) {
                if ($item['code'] === $code) {
                    $matchingItem = $item;
                    break;
                }
            }

            if ($matchingItem !== null) {
                $mergedItem = array_merge($mergedItem, $matchingItem);
            }

            if (isset($totalQRCode[$code])) {
                $mergedItem = array_merge($mergedItem, $totalQRCode[$code]);
            }

            $mergedData[] = $mergedItem;
        }

return collect($mergedData);


    }

    public function startCell(): string
    {
        return 'A2';
    }
}
