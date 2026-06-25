<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PrefixSeeder::class,
            BranchSeeder::class,
            DocumentTypeSeeder::class,
            UserSeeder::class,
            /* CategorySeeder::class,
            SubcategorySeeder::class,
            BrandSeeder::class,
            SpecificationSeeder::class, */
            AddressSeeder::class,
            PaymentMethodSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }
}
