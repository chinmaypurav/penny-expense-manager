<?php

use App\Enums\Frequency;
use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Jobs\TransactRecurringExpenseJob;
use App\Jobs\TransactRecurringIncomeJob;
use App\Jobs\TransactRecurringTransferJob;
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

Schedule::job(new TransactRecurringExpenseJob(Frequency::DAILY))
    ->daily();
Schedule::job(new TransactRecurringExpenseJob(Frequency::WEEKLY))
    ->weekly();
Schedule::job(new TransactRecurringExpenseJob(Frequency::MONTHLY))
    ->monthly();
Schedule::job(new TransactRecurringExpenseJob(Frequency::QUARTERLY))
    ->quarterly();
Schedule::job(new TransactRecurringExpenseJob(Frequency::YEARLY))
    ->yearly();

Schedule::job(new TransactRecurringTransferJob(Frequency::DAILY))
    ->daily();
Schedule::job(new TransactRecurringTransferJob(Frequency::WEEKLY))
    ->weekly();
Schedule::job(new TransactRecurringTransferJob(Frequency::MONTHLY))
    ->monthly();
Schedule::job(new TransactRecurringTransferJob(Frequency::QUARTERLY))
    ->quarterly();
Schedule::job(new TransactRecurringTransferJob(Frequency::YEARLY))
    ->yearly();
