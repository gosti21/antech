<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            'gaming',
            'oficina'
        ];

        $categories = Category::pluck('id')->toArray();

        foreach ($categories as $category){
            foreach ($subcategories as $subcategory){
                Subcategory::create([
                    'name' => $subcategory,
                    'category_id' => $category
                ]);
            }
        }
    }
}
