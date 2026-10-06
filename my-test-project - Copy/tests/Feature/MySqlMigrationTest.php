<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MySqlMigrationTest extends TestCase
{
    public function test_catalog_migrations_use_mysql_compatible_identifier_lengths(): void
    {
        // Compile the real migrations as MySQL SQL without touching a server.
        config([
            'database.default' => 'mysql',
        ]);
        Schema::clearResolvedInstance('db.schema');

        $queries = DB::connection('mysql')->pretend(function () {
            foreach (glob(database_path('migrations/2026_10_06_*.php')) as $path) {
                $migration = require $path;
                $migration->up();
            }
        });

        $this->assertNotEmpty($queries);

        foreach ($queries as $query) {
            preg_match_all('/`([^`]+)`/', $query['query'], $identifiers);

            foreach ($identifiers[1] as $identifier) {
                $this->assertLessThanOrEqual(64, strlen($identifier), $query['query']);
            }
        }
    }
}
