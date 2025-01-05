<?php

namespace App\Console\Commands\Penny;

use App\Models\User;
use Database\Seeders\ProductionSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InitCommand extends Command
{
    protected $signature = 'penny:init';

    protected $description = 'Initialize Penny Project';

    public function handle(): void
    {
        if (User::count()) {
            $this->warn('Project already initialized.');

            return;
        }

        $email = $this->ask('Enter email');
        $password = $this->secret('Enter password');
        $name = $this->ask('Enter name', Str::before($email, '@'));

        User::create([
            'email' => $email,
            'name' => $name,
            'password' => $password,
        ]);

        $this->info('User created.');
        $this->newLine();
        $this->info('Running Seeders...');

        $this->call('db:seed', ['--class' => ProductionSeeder::class]);
    }
}
