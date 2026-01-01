<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $columns;
    protected $options;

    public function __construct($data, $columns = [], $options = [])
    {
        $this->data = $data;
        $this->columns = $columns;
        $this->options = $options;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Weekly Data Sheet
        if (!empty($this->data['weekly']) && $this->data['weekly']->count()) {
            $sheets[] = new ReportSheet($this->data['weekly'], 'Weekly Production', [
                'Year', 'Week', 'Total Crates'
            ]);
        }

        // 2. Monthly Data Sheet
        if (!empty($this->data['monthly']) && $this->data['monthly']->count()) {
            $formattedMonthly = $this->data['monthly']->map(function($r) {
                return [
                    'Year' => $r->year,
                    'Month' => \Carbon\Carbon::createFromDate($r->year, $r->month_num, 1)->format('F'),
                    'Total Crates' => $r->total
                ];
            });
            $sheets[] = new ReportSheet($formattedMonthly, 'Monthly Production', array_keys($formattedMonthly->first()));
        }

        // 3. Profitability Sheet
        if (!empty($this->data['profitability']) && $this->data['profitability']->count()) {
            $formattedProfit = $this->data['profitability']->map(function($r) {
                return [
                    'Breed' => $r->breed,
                    'Type' => $r->type,
                    'Sales' => $r->sales,
                    'Feed Cost' => $r->feed_cost,
                    'Operational Cost' => $r->operational_cost ?? 0,
                    'Net Profit' => $r->profit
                ];
            });
            $sheets[] = new ReportSheet($formattedProfit, 'Profitability', array_keys($formattedProfit->first()));
        }

        // 4. Custom Metrics Sheets
        if (!empty($this->data['sales']) && $this->data['sales']->count()) {
            $formattedSales = $this->data['sales']->map(function($s) {
                return [
                    'Date' => $s->sale_date, // Export raw date for Excel formatting
                    'Customer' => $s->customer->name ?? 'N/A',
                    'Product' => $s->saleable_type === 'App\Models\Bird' ? 'Bird' : 'Egg',
                    'Quantity' => $s->quantity,
                    'Total Amount' => $s->total_amount,
                ];
            });
            $sheets[] = new ReportSheet($formattedSales, 'Sales History', array_keys($formattedSales->first()));
        }

        if (empty($sheets)) {
            // Fallback empty sheet
            $sheets[] = new ReportSheet(collect([]), 'No Data', []);
        }

        return $sheets;
    }
}

// Inner class for individual sheets
class ReportSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $collection;
    protected $title;
    protected $headings;

    public function __construct($collection, $title, $headings)
    {
        $this->collection = $collection;
        $this->title = $title;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return substr($this->title, 0, 30); // Excel limit
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold Header row with Blue Background
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF2C3E50']],
            ],
        ];
    }
}