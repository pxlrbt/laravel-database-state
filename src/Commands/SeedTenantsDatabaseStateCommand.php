<?php

namespace pxlrbt\LaravelDatabaseState\Commands;

use Illuminate\Database\QueryException;
use Stancl\Tenancy\Concerns\HasTenantOptions;
use Stancl\Tenancy\Database\Exceptions\TenantDatabaseDoesNotExistException;

class SeedTenantsDatabaseStateCommand extends SeedDatabaseStateCommand
{
    use HasTenantOptions;

    protected $signature = 'tenants:db:seed-state';

    protected $description = 'Seed tenants database state';

    public function handle(): int
    {
        $this->components->info('Seeding tenants database state.');

        foreach ($this->getTenants() as $tenant) {
            try {
                $tenant->run(function ($tenant) {
                    $this->components->info("Tenant {$tenant->getTenantKey()}");

                    // Seed
                    parent::seedStates();
                });
            } catch (TenantDatabaseDoesNotExistException|QueryException $th) {
                if (! $this->option('skip-failing')) {
                    throw $th;
                }
            }
        }

        return self::SUCCESS;
    }

    protected function getFilePath(): string
    {
        return database_path('states/Tenant');
    }
}
