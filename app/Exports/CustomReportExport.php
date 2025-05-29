<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = [];
        if (!empty($this->data['eggs'])) {
            foreach ($this->data['eggs'] as $egg) {
                $rows[] = ['Type' => 'Egg', 'Date' => $egg->date_laid, 'Quantity' => $egg->quantity ?? 1];
            }
        }
        if (!empty($this->data['sales'])) {
            foreach ($this->data['sales'] as $sale) {
                $rows[] = [
                    'Type' => 'Sale',
                    'Date' => $sale->sale_date,
                    'Customer' => $sale->customer->name ?? 'N/A',
                    'Item' => $sale->saleable ? class_basename($sale->saleable) . ' #' . $sale->saleable->id : 'N/A',
                    'Quantity' => $sale->quantity,
                    'Total' => $sale->total_amount,
                ];
            }
        }
        if (!empty($this->data['expenses'])) {
            foreach ($this->data['expenses'] as $expense) {
                $rows[] = [
                    'Type' => 'Expense',
                    'Date' => $expense->date,
                    'Description' => $expense->description ?? 'N/A',
                    'Amount' => $expense->amount,
                ];
            }
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return ['Type', 'Date', 'Customer/Item/Description', 'Quantity/Amount'];
    }
}