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

        return self::SUCCESS;
    }

    protected function getStateClasses(): Collection
    {
        return collect(app(Filesystem::class)->allFiles(database_path('States')))
            ->map(fn ($file) => str($file->getPathname())
                ->replace([base_path(), '.php', '/'], ['', '', '\\'])
                ->ltrim('\\')
                ->ucfirst()
                ->toString()
            );
    }
}
