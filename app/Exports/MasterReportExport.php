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
            $loginHistory['moreThan2Count']!==0 ? $loginHistory['moreThan2Count'] : '0',
            $loginHistory['moreThan5Count']!==0 ? $loginHistory['moreThan5Count'] : '0',
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
        $mergedData = \App\Models\WD::leftJoin('cities', 'wd.city_id', '=', 'cities.id')
            ->leftJoin('q_r_code_items', 'wd.id', '=', 'q_r_code_items.wd_id')
            ->leftJoin('login_histories', 'q_r_code_items.id', '=', 'login_histories.q_r_code_item_id')
            ->select(
                'wd.id',
                'wd.code',
                'cities.name AS city',
                \DB::raw('COUNT(DISTINCT login_histories.retailer_id) AS totalRetailers'),
                \DB::raw('SUM(q_r_code_items.is_redeemed) AS redeemed'), // Use the correct column name
                \DB::raw('(SELECT COUNT(id) FROM q_r_code_items qr WHERE qr.wd_id = wd.id) AS total')
            )
            ->groupBy('wd.id', 'wd.code', 'cities.name')
            ->with('qRCodeItems:id,wd_id')
            ->oldest('wd.created_at')
            ->get()
            ->map(function ($item) {
                $total = $item->total;
                $redeemed = $item->redeemed;
                $percentage = $total > 0 ? ($redeemed / $total) : 0;
                $formattedPercentage = number_format($percentage * 100, 2);

                return [
                    $item->code => [
                        'city' => $item->city,
                        'code' => $item->code,
                        'total' => $total,
                        'redeemed' => $redeemed,
                        'percentage' => $formattedPercentage . '%',
                        'totalRetailers' => $item->totalRetailers ?? 0,
                        'moreThan2Count' => 0,
                        'moreThan5Count' => 0,
                    ]
                ];
            })
            ->collapse()
            ->toArray();

        $loginHistories = \App\Models\LoginHistory::with([
            'retailer:id',
            'qRCodeItem:id,wd_id',
            'qRCodeItem.wd:id,code'
        ])
        ->select('id', 'retailer_id', 'q_r_code_item_id', 'created_at')
        ->get()
        ->groupBy('qRCodeItem.wd.code') // Grouping by wd code
        ->map(function ($histories, $wdCode) {
            $retailerData = $histories->groupBy('retailer_id')->map(function ($retailerHistories) {
                return $retailerHistories->count();
            });
            return $retailerData;
        });


        $retailersWithQtyWiseCount = $loginHistories->map(function ($wdData) {
            $moreThan2Count = $wdData->filter(function ($counts) {
                return $counts >= 2;
            })->count();

            $moreThan5Count = $wdData->filter(function ($counts) {
                return $counts >= 5;
            })->count();

            return [
                "moreThan2Count" => $moreThan2Count,
                "moreThan5Count" => $moreThan5Count,
            ];
        })->toArray();

        foreach ($loginHistories as $wdCode => $wdData) {
            if (isset($mergedData[$wdCode])) {
                $counts = $retailersWithQtyWiseCount[$wdCode];
                $mergedData[$wdCode]['moreThan2Count'] = $counts['moreThan2Count'];
                $mergedData[$wdCode]['moreThan5Count'] = $counts['moreThan5Count'];
            }
        }

return collect($mergedData);


    }

    public function startCell(): string
    {
        return 'A2';
    }
}
