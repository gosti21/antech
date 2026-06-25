<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documents = [
            'DNI',
            'CE',
            'RUC',
        ];

        foreach ($documents as $document) {
            DocumentType::create([
                'type' => $document
            ]);
        }
    }
}
