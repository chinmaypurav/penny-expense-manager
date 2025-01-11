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

it('returns the minimum of iterations left or the differences', function () {
    $start = Carbon::create(2023, 1, 1);
    $end = Carbon::create(2023, 1, 5); // 4 days
    $iterationsLeft = 2;

    $remainingOccurrences = Frequency::DAILY->getRemainingIterations($start, $end, $iterationsLeft);

    expect($remainingOccurrences)->toEqual(2);
});
