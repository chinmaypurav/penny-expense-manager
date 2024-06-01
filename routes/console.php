<?php

use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new CreatePeriodicalBalanceEntryJob(RecordType::MONTHLY, today()))
    ->monthly();

Schedule::job(new CreatePeriodicalBalanceEntryJob(RecordType::YEARLY, today()))
    ->yearly();
