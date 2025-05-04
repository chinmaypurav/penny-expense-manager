<?php

use App\Enums\RecordType;
use Carbon\CarbonImmutable;

it('returns start date', function (RecordType $recordType, CarbonImmutable $inputDate, CarbonImmutable $expectedStartDate) {
    $this->assertTrue($recordType->getStartDate($inputDate)->eq($expectedStartDate));
})->with([
    [RecordType::INITIAL, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-02-15 00:00:00')],
    [RecordType::MONTHLY, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-02-01 00:00:00')],
    [RecordType::YEARLY, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-01-01 00:00:00')],
]);

it('returns end date', function (RecordType $recordType, CarbonImmutable $inputDate, CarbonImmutable $expectedEndDate) {
    $this->assertTrue($recordType->getEndDate($inputDate)->is($expectedEndDate));
})->with([
    [RecordType::INITIAL, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-02-15 23:59:59')],
    [RecordType::MONTHLY, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-02-28 23:59:59')],
    [RecordType::YEARLY, CarbonImmutable::parse('2025-02-15'), CarbonImmutable::parse('2025-12-31 23:59:59')],
]);
