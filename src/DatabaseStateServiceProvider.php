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
            ->name('database-state')
            ->hasConfigFile()
            ->hasCommands([
                Commands\MakeCommand::class,
                Commands\SeedDatabaseStateCommand::class,
            ]);

        if (class_exists('\Stancl\Tenancy\Concerns\HasTenantOptions')) {
            $package->hasCommands([
                Commands\SeedTenantsDatabaseStateCommand::class,
            ]);
        }
    }

    public function packageBooted()
    {
        if (config('database-state.run_after_migration') !== true) {
            return;
        }

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

        if (str($event->command)->startsWith('migrate')) {
            Artisan::call(Commands\SeedDatabaseStateCommand::class, [], $event->output);
        }

        if (str($event->command)->startsWith('tenants:migrate')) {
            Artisan::call(Commands\SeedTenantsDatabaseStateCommand::class, [], $event->output);
        }
    }
}
