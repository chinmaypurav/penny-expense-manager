<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AccountTransactionsExport implements FromCollection, ShouldQueue, WithHeadings
{
    public function __construct(public Collection $transactions) {}

    public function collection(): Collection
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'sr',
            'date',
            'description',
            'amount',
            'type',
        ];
    }
}
