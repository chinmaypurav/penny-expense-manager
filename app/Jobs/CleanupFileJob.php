<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\File;

class CleanupFileJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $filePath) {}

    public function handle(): void
    {
        File::delete(storage_path("app/{$this->filePath}"));
    }
}
