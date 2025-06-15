<?php

use App\Jobs\CleanupFileJob;
use Illuminate\Support\Facades\File;

test('it deletes file', function () {
    $mock = File::partialMock();

    $mock->expects('delete')->once()->andReturn(true);

    CleanupFileJob::dispatch('test.csv');
});
