<?php

use App\Jobs\CleanupFileJob;

test('it deletes file', function () {
    $mock = \Illuminate\Support\Facades\File::partialMock();

    $mock->expects('delete')->once()->andReturn(true);

    CleanupFileJob::dispatch('test.csv');
});
