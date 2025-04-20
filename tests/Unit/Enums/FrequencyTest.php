<?php

use App\Enums\Frequency;
use Illuminate\Support\Carbon;

it('returns correct remaining occurrences for daily frequency', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 1, 10);
    $iterationsLeft = 5;

    $remainingOccurrences = Frequency::DAILY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(5);
});

it('returns correct remaining occurrences for weekly frequency', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 2, 1); // 4 weeks
    $iterationsLeft = 2;

    $remainingOccurrences = Frequency::WEEKLY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(2);
});

it('returns correct remaining occurrences for monthly frequency', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 6, 1); // 6 months
    $iterationsLeft = 10;

    $remainingOccurrences = Frequency::MONTHLY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(6);
});

it('returns correct remaining occurrences for quarterly frequency', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2024, 1, 1); // 4 quarters
    $iterationsLeft = 3;

    $remainingOccurrences = Frequency::QUARTERLY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(3);
});

it('returns correct remaining occurrences for yearly frequency', function () {
    $start = Carbon::create(2020, 1, 1);
    $end = Carbon::create(2025, 1, 1); // 5 years
    $iterationsLeft = 2;

    $remainingOccurrences = Frequency::YEARLY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(2);
});

it('returns one for once frequency', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 12, 31);
    $iterationsLeft = 10;

    $remainingOccurrences = Frequency::ONCE->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(1);
});

it('returns the iterations within the duration when the iteration is above the date range', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 1, 5); // 5 days
    $iterationsLeft = 10;

    $remainingOccurrences = Frequency::DAILY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(5);
});

it('returns the iterations within the date range when remaining recurrences is null', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 1, 5); // 5 days
    $iterationsLeft = null;

    $remainingOccurrences = Frequency::DAILY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(5);
});

it('returns the next transaction date', function (Frequency $frequency, Carbon $nextTransactionDate) {
    $this->assertTrue(
        $frequency->getNextTransactionDate(Carbon::today())->is($nextTransactionDate)
    );

})->with([
    [Frequency::DAILY, Carbon::today()->addDay()],
    [Frequency::WEEKLY, Carbon::today()->addWeek()],
    [Frequency::MONTHLY, Carbon::today()->addMonth()],
    [Frequency::QUARTERLY, Carbon::today()->addQuarter()],
    [Frequency::YEARLY, Carbon::today()->addYear()],
    [Frequency::ONCE, Carbon::today()],
]);
