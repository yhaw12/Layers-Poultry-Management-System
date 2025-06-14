<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomReportExport implements FromCollection, WithHeadings, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $collection = collect();

        if (isset($this->data['weekly'])) {
            foreach ($this->data['weekly'] as $row) {
                $collection->push([
                    'Year' => $row->year,
                    'Week' => $row->week,
                    'Total Eggs' => $row->total,
                ]);
            }
        } elseif (isset($this->data['monthly'])) {
            foreach ($this->data['monthly'] as $row) {
                $collection->push([
                    'Year' => $row->year,
                    'Month' => \Carbon\Carbon::create()->month($row->month_num)->format('F'),
                    'Total Eggs' => $row->total,
                ]);
            }
        } elseif (isset($this->data['profitability'])) {
            foreach ($this->data['profitability'] as $row) {
                $collection->push([
                    'Bird ID' => $row->bird_id,
                    'Breed' => $row->breed,
                    'Sales ($)' => number_format($row->sales, 2),
                    'Feed Cost ($)' => number_format($row->feed_cost, 2),
                    'Expenses ($)' => number_format($row->expenses, 2),
                    'Profit ($)' => number_format($row->profit, 2),
                ]);
            }
        } elseif (isset($this->data['eggs']) || isset($this->data['sales']) || isset($this->data['expenses'])) {
            if (isset($this->data['eggs'])) {
                $collection->push(['Eggs']);
                foreach ($this->data['eggs'] as $egg) {
                    $collection->push([
                        'Date Laid' => $egg->date_laid,
                        'Quantity' => $egg->sold_quantity ?? 1,
                    ]);
                }
                $collection->push([]);
            }
            if (isset($this->data['sales'])) {
                $collection->push(['Sales']);
                foreach ($this->data['sales'] as $sale) {
                    $collection->push([
                        'Date' => $sale->sale_date,
                        'Customer' => $sale->customer->name ?? 'N/A',
                        'Item' => $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A',
                        'Quantity' => $sale->quantity,
                        'Total ($)' => number_format($sale->total_amount, 2),
                    ]);
                }
                $collection->push([]);
            }
            if (isset($this->data['expenses'])) {
                $collection->push(['Expenses']);
                foreach ($this->data['expenses'] as $expense) {
                    $collection->push([
                        'Date' => $expense->date,
                        'Description' => $expense->description ?? 'N/A',
                        'Amount ($)' => number_format($expense->amount, 2),
                    ]);
                }
            }
        }

        return $collection;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Report';
    }
}