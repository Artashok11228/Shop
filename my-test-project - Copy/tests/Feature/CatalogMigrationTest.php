<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CatalogMigrationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_seeded_catalog_can_be_rolled_back_and_migrated_again(): void
    {
        $this->seed();

        $this->artisan('migrate:rollback', ['--step' => 7])->assertSuccessful();

        $this->assertFalse(Schema::hasTable('categories'));
        $this->assertFalse(Schema::hasTable('products'));
        $this->assertFalse(Schema::hasTable('attribute_value_product'));
        $this->assertSame(1, (int) DB::selectOne('PRAGMA foreign_keys')->foreign_keys);

        $this->artisan('migrate', ['--seed' => true])->assertSuccessful();

        $this->assertDatabaseCount('users', 10);
        $this->assertDatabaseCount('categories', 13);
        $this->assertDatabaseCount('products', 36);
        $this->assertDatabaseCount('attribute_value_product', 149);
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
    }
}
