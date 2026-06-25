<?php

namespace Database\Seeders;

use App\Models\Specification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specifications = [
            'Almacenamiento',
            'DPI',
            'Dimensiones'
        ];

        foreach ($specifications as $specification) {
            Specification::create([
                'name' => $specification
            ]);
        }
    }
}
