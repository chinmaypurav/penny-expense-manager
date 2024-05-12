<?php

namespace App\Filament\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait CashFlowChartTrait
{
    private function processMissingDates(array $amounts): array
    {
        $dates = array_keys($amounts);
        $startDate = Carbon::parse(Arr::first($dates));
        $endDate = Carbon::parse(Arr::last($dates));

        $data = [];
        for ($i = $startDate; $i->lessThanOrEqualTo($endDate); $i->addDay()) {
            $data[$i->toDateString()] = Arr::get($amounts, $i->toDateString(), 0);
        }

        return $data;
    }
}
