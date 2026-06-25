<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'niubiz',  'type' => 'other', 'required_qr' => false],
            ['name' => 'cash',  'type' => 'cash', 'required_qr' => false],
            ['name' => 'card',  'type' => 'card', 'required_qr' => false],
            ['name' => 'yape',  'type' => 'wallet', 'required_qr' => true],
            ['name' => 'plin',  'type' => 'wallet', 'required_qr' => true],
            ['name' => 'transfers', 'type' => 'other', 'required_qr' => false],
            ['name' => 'deposits', 'type' => 'other', 'required_qr' => false],
            ['name' => 'others', 'type' => 'other', 'required_qr' => false],
        ];

        foreach ($methods as $method) {
            PaymentMethod::create($method);
        }
    }
}
