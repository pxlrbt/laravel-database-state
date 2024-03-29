<?php

namespace pxlrbt\LaravelDatabaseState\Commands;

use Database\States\UserState;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SeedDatabaseStateCommand extends Command
{
    protected $signature = 'db:seed-state';

    protected $description = 'Seed database state';

    public function handle(): int
    {
        $this->components->info('Seeding database state.');

        $this->seedStates();

        return self::SUCCESS;
    }

    protected function seedStates(): void
    {
        $this->getStateClasses()->each(function ($class) {
            $this->components->twoColumnDetail(
                $class,
                '<fg=yellow;options=bold>RUNNING</>'
            );

            $startTime = microtime(true);

            (new $class)->__invoke();

            $runTime = number_format((microtime(true) - $startTime) * 1000, 2);

            $this->components->twoColumnDetail(
                $class,
                "<fg=gray>$runTime ms</> <fg=green;options=bold>DONE</>"
            );
        });
    }

    protected function getFilePath(): string
    {
        return database_path('states');
    }

    protected function getStateClasses(): Collection
    {
        $filesystem = app(Filesystem::class);

        if ($filesystem->missing($this->getFilePath())) {
            if (! $this->components->confirm("You don't have a `states` folder in your database folder. Do you want to create it?", true)) {
                return collect();
            }

            $filesystem->ensureDirectoryExists($this->getFilePath());
        }

        return collect(app(Filesystem::class)->files($this->getFilePath()))
            ->map(fn ($file) => str($file->getPathname())
                ->replace([base_path(), '.php', 'database/states', '/'], ['', '', 'Database\\States', '\\'])
                ->ltrim('\\')
                ->toString()
            );
    }
}
