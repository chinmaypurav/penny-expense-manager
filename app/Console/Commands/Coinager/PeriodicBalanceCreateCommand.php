<?php

namespace App\Console\Commands\Coinager;

use App\Enums\RecordType;
use App\Jobs\CreatePeriodicalBalanceEntryJob;
use App\Models\Account;
use App\Models\Balance;
use Carbon\CarbonImmutable as Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PeriodicBalanceCreateCommand extends Command
{
    protected $signature = 'coinager:periodic-balance:create';

    protected $description = 'Create periodic balance records for accounts starting from the oldest initial date';

    public function handle(): void
    {
        try {
            /**
             * @var $oldestDate Carbon
             */
            $oldestDate = Account::oldest('initial_date')->valueOrFail('initial_date');
        } catch (ModelNotFoundException) {
            $this->warn('No accounts found. Please initialize Coinager Project first.');

            return;
        }

        $recordTypes = [RecordType::MONTHLY->value, RecordType::YEARLY->value];

        $recordType = $this->choice('Record Type: ', $recordTypes);

        $recordType = match ($recordType) {
            'monthly' => RecordType::MONTHLY,
            'yearly' => RecordType::YEARLY,
        };

        $today = today();
        $months = [];
        for ($d = $oldestDate->toMutable(); $d->lte($today); $d->addMonth()) {
            $end = $recordType->getEndDate($d);

            if ($end->gte($today)) {
                break;
            }

            $months[$d->toDateString()] = $d->format('M-y');
        }

        if (empty($months)) {
            $this->warn('No account is old enough to create historical balance entries.');

            return;
        }

        $month = $this->choice('Select month: ', $months);

        if (
            $balance = Balance::query()
                ->where('record_type', $recordType)
                ->whereDate('recorded_until', $month)
                ->first()
        ) {
            if (! $this->confirm('Balance already exists for the selected month. Overwrite?')) {
                $this->info('Balance entry creation cancelled.');

                return;
            }
            $balance->delete();
        }

        CreatePeriodicalBalanceEntryJob::dispatchSync($recordType, Carbon::parse($month));
        $this->info('Balance entry created successfully.');
    }
}
