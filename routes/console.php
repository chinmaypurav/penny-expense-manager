<?php

use App\Enums\Frequency;
use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Jobs\TransactRecurringIncomeJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CreatePeriodicalBalanceEntryJob(RecordType::MONTHLY, today()))
    ->monthly();

Schedule::job(new CreatePeriodicalBalanceEntryJob(RecordType::YEARLY, today()))
    ->yearly();

Schedule::job(new TransactRecurringIncomeJob(Frequency::DAILY))
    ->daily();
Schedule::job(new TransactRecurringIncomeJob(Frequency::WEEKLY))
    ->weekly();
Schedule::job(new TransactRecurringIncomeJob(Frequency::MONTHLY))
    ->monthly();
Schedule::job(new TransactRecurringIncomeJob(Frequency::QUARTERLY))
    ->quarterly();
Schedule::job(new TransactRecurringIncomeJob(Frequency::YEARLY))
    ->yearly();
