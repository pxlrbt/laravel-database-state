<?php

namespace pxlrbt\LaravelDatabaseState;

use App\Console\Commands\SeedDatabaseStateCommand;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DatabaseStateServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-db-state')
            ->hasCommands([
                Commands\MakeCommand::class,
                Commands\SeedDatabaseStateCommand::class
            ]);
    }

    public function bootingPackage()
    {

        Event::listen(
            CommandFinished::class,
            [$this, 'runDatabaseStateSeeder'],
        );
    }


    public function runDatabaseStateSeeder(CommandFinished $event): void
    {
        if ($event->exitCode !== 0) {
            return;
        }

        if (! str($event->command)->startsWith('migrate')) {
            return;
        }

        Artisan::call(Commands\SeedDatabaseStateCommand::class, [], $event->output);
    }
}
