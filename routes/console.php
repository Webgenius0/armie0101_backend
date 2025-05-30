<?php

use App\Console\Commands\MakeService;
use App\Console\Commands\ProcessQueue;
use App\Console\Commands\ReactivateBannedUsers;
use App\Console\Commands\UpdateUserAvailability;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//# registering make service class command
Artisan::command('make:service {name}', function ($name) {
    $this->call(MakeService::class, ['name' => $name]);
});

Schedule::command(ReactivateBannedUsers::class)->everyMinute();
Schedule::command(ProcessQueue::class)->everyMinute();
Schedule::command(UpdateUserAvailability::class)->everyMinute();
