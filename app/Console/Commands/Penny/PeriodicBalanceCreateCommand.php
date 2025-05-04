<?php

namespace App\Console\Commands\Penny;

use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Models\Account;
use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class PeriodicBalanceCreateCommand extends Command
{
    protected $signature = 'penny:periodic-balance:create';

    protected $description = 'Create periodic balance records for accounts starting from the oldest initial date';

    public function handle(): void
    {
        try {
            /**
             * @var $oldestDate Carbon
             */
            $oldestDate = Account::oldest('initial_date')->valueOrFail('initial_date');
        } catch (ModelNotFoundException) {
            $this->warn('No accounts found. Please initialize Penny Project first.');

            return;
        }

        $recordTypes = [RecordType::MONTHLY->value, RecordType::YEARLY->value];

        $recordType = $this->choice('Record Type: ', $recordTypes);

        $recordType = match ($recordType) {
            'monthly' => RecordType::MONTHLY,
            'yearly' => RecordType::YEARLY,
        };

        $today = today()->subDay();
        $choices = [];
        for ($d = $oldestDate; $d->lte($today); $d->addMonth()) {
            $d->startOfMonth();
            $choices[$d->toDateString()] = $d->format('M-y');
        }

        $choice = $this->choice('Select month: ', $choices);

        if ($balance = Balance::query()
            ->where('record_type', $recordType)
            ->whereDate('recorded_until', $choice)->first()
        ) {
            if (! $this->confirm('Balance already exists for the selected month. Overwrite?')) {
                $this->info('Balance entry creation cancelled.');

                return;
            }
            $balance->delete();
        }

        CreatePeriodicalBalanceEntryJob::dispatchSync($recordType, Carbon::parse($choice));
        $this->info('Balance entry created successfully.');
    }
}
