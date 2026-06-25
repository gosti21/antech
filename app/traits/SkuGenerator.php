<?php

namespace App\traits;

use App\Models\Product;

trait SkuGenerator
{
    public function generateSkuVariant(int $id): string
    {
        $name = Product::find($id)->name;
        $prefix = strtoupper('var');
        $randomNumbers = rand(1000, 9999);
        $namePrefix = strtoupper(substr($name, 0, 3));

        return $prefix . $randomNumbers . $namePrefix;
    }
}
