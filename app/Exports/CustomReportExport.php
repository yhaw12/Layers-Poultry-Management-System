<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class CustomReportExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $rows = new Collection();
        if (isset($this->data['eggs'])) {
            foreach ($this->data['eggs'] as $egg) {
                $rows->push([
                    'Type' => 'Egg',
                    'Date' => $egg->date_laid,
                    'Crates' => $egg->crates,
                    'Sold' => $egg->sold_quantity ?? 0,
                ]);
            }
        }
        // Add sales, expenses similarly
        return $rows;
    }
}