<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class CustomReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $data;
    protected $columns;

    public function __construct($data, $columns = [])
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. EXECUTIVE DASHBOARD
        $dashboardData = collect([
            ['Metric', 'Value'],
            ['Total Income', $this->data['profit_loss']['total_income'] ?? 0],
            ['Total Expenses (Inc. Payroll)', ($this->data['profit_loss']['total_expenses'] ?? 0) + ($this->data['profit_loss']['total_payroll'] ?? 0)],
            ['Net Profit', $this->data['profit_loss']['profit_loss'] ?? 0],
            ['', ''], // Spacer
            ['Avg Crates Per Day', $this->data['avg_crates_per_day'] ?? 0],
            ['Feed Conversion Ratio (FCR)', $this->data['efficiency']['fcr'] ?? 0],
            ['Total Feed Consumed (kg)', $this->data['efficiency']['total_feed'] ?? 0],
            ['Unsold Inventory Value (Dead Money)', $this->data['advanced_metrics']['dead_money'] ?? 0],
        ]);
        $sheets[] = new AdvancedSheet($dashboardData, 'Executive Summary', [], ['B' => '"₵"#,##0.00_-']);

        // 2. MONTHLY PRODUCTION VS SALES
        if (!empty($this->data['monthly_summary']) && count($this->data['monthly_summary']) > 0) {
            $formattedMonthly = collect($this->data['monthly_summary'])->map(function($r) {
                return [
                    'Year' => $r->year,
                    'Month' => Carbon::createFromDate($r->year, $r->month_num, 1)->format('F'),
                    'Crates Produced' => $r->crates_produced,
                    'Crates Sold' => $r->crates_sold,
                    'Sales Revenue' => $r->revenue
                ];
            });

            // Add Dynamic Formula Row at the bottom
            $rowCount = $formattedMonthly->count() + 1; 
            $formattedMonthly->push([
                'Year' => 'TOTALS', 'Month' => '',
                'Crates Produced' => "=SUM(C2:C{$rowCount})",
                'Crates Sold' => "=SUM(D2:D{$rowCount})",
                'Sales Revenue' => "=SUM(E2:E{$rowCount})"
            ]);

            $sheets[] = new AdvancedSheet($formattedMonthly, 'Monthly Crates & Sales', array_keys($formattedMonthly->first()), [
                'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'E' => '"₵"#,##0.00_-',
            ]);
        }

        // 3. SALES LEDGER
        if (!empty($this->data['sales_history']) && count($this->data['sales_history']) > 0) {
            $formattedSales = collect($this->data['sales_history'])->map(function($s) {
                return [
                    'Date' => Carbon::parse($s->sale_date)->format('Y-m-d'),
                    'Customer' => $s->customer->name ?? 'Walk-in',
                    'Product' => str_replace('App\\Models\\', '', $s->saleable_type),
                    'Quantity' => $s->quantity,
                    'Unit Price' => $s->unit_price,
                    'Total Amount' => $s->total_amount,
                    'Status' => ucfirst($s->status)
                ];
            });

            $rowCount = $formattedSales->count() + 1;
            $formattedSales->push([
                'Date' => 'TOTALS', 'Customer' => '', 'Product' => '',
                'Quantity' => "=SUM(D2:D{$rowCount})", 'Unit Price' => '',
                'Total Amount' => "=SUM(F2:F{$rowCount})", 'Status' => ''
            ]);

            $sheets[] = new AdvancedSheet($formattedSales, 'Sales Ledger', array_keys($formattedSales->first()), [
                'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                'E' => '"₵"#,##0.00_-',
                'F' => '"₵"#,##0.00_-',
            ]);
        }

        // 4. EXPENSE LEDGER
        if (!empty($this->data['expense_history']) && count($this->data['expense_history']) > 0) {
            $formattedExpenses = collect($this->data['expense_history'])->map(function($e) {
                return [
                    'Date' => Carbon::parse($e->date)->format('Y-m-d'),
                    'Category' => ucfirst($e->category),
                    'Description' => $e->description ?? 'N/A',
                    'Amount' => $e->amount,
                ];
            });

            $rowCount = $formattedExpenses->count() + 1;
            $formattedExpenses->push([
                'Date' => 'TOTALS', 'Category' => '', 'Description' => '',
                'Amount' => "=SUM(D2:D{$rowCount})"
            ]);

            $sheets[] = new AdvancedSheet($formattedExpenses, 'Expense Ledger', array_keys($formattedExpenses->first()), [
                'D' => '"₵"#,##0.00_-',
            ]);
        }

        if (empty($sheets)) {
            $sheets[] = new AdvancedSheet(collect([['Message' => 'No data found for this date range']]), 'No Data', ['Message']);
        }

        return $sheets;
    }
}

// Highly customized internal class for styling the sheets
class AdvancedSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithColumnFormatting, WithEvents
{
    protected $collection;
    protected $title;
    protected $headings;
    protected $columnFormats;

    public function __construct($collection, $title, $headings = [], $columnFormats = [])
    {
        $this->collection = $collection;
        $this->title = $title;
        $this->headings = $headings;
        $this->columnFormats = $columnFormats;
    }

    public function collection() { return $this->collection; }
    public function headings(): array { return $this->headings; }
    public function title(): string { return substr($this->title, 0, 30); }
    public function columnFormats(): array { return $this->columnFormats; }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        
        // Style Headers if they exist
        if (!empty($this->headings)) {
            $styles[1] = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A8A']], // Tailwind Blue-900
            ];
        }

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $cellRange = 'A1:' . $highestColumn . $highestRow;

                // 1. Add AutoFilter to the header row (Makes it easy to sort by customer, date, etc.)
                if (!empty($this->headings)) {
                    $sheet->setAutoFilter('A1:' . $highestColumn . '1');
                }

                // 2. Add light borders to all data cells
                $sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD1D5DB'], // Tailwind Gray-300
                        ],
                    ],
                ]);

                // 3. Highlight the TOTALS row at the bottom (Bold text, Light Gray Background)
                $lastRowValue = $sheet->getCell('A' . $highestRow)->getValue();
                if ($lastRowValue === 'TOTALS') {
                    $sheet->getStyle('A' . $highestRow . ':' . $highestColumn . $highestRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF3F4F6']], // Tailwind Gray-100
                        'borders' => [
                            'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF000000']],
                        ]
                    ]);
                }
            },
        ];
    }
}