<?php

use App\Enums\AccountType;

it('checks getShortCode output', function (AccountType $accountType, string $expectedShortCode) {
    expect($accountType->getShortCode())->toBe($expectedShortCode);
})->with([
    [AccountType::SAVINGS, 'SB'],
    [AccountType::CURRENT, 'CC'],
    [AccountType::CREDIT, 'CR'],
    [AccountType::LOAN, 'LN'],
    [AccountType::TRADING, 'TR'],
    [AccountType::CASH, 'CA'],
]);
