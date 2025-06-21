<?php

use App\Enums\RecordType;
use Carbon\CarbonImmutable as Carbon;

it('returns start date', function (RecordType $recordType, Carbon $inputDate, Carbon $expectedStartDate) {
    $this->assertTrue($recordType->getStartDate($inputDate)->eq($expectedStartDate));
})->with([
    [RecordType::MONTHLY, Carbon::parse('2025-02-15'), Carbon::parse('2025-02-01 00:00:00')],
    [RecordType::YEARLY, Carbon::parse('2025-02-15'), Carbon::parse('2025-01-01 00:00:00')],
]);

it('returns end date', function (RecordType $recordType, Carbon $inputDate, Carbon $expectedEndDate) {
    $this->assertTrue($recordType->getEndDate($inputDate)->is($expectedEndDate));
})->with([
    [RecordType::MONTHLY, Carbon::parse('2025-02-15'), Carbon::parse('2025-02-28 23:59:59')],
    [RecordType::YEARLY, Carbon::parse('2025-02-15'), Carbon::parse('2025-12-31 23:59:59')],
]);
