<?php

namespace App\Repositories\Api\v1\Shop;

use App\Contracts\Api\v1\Shop\CategorySInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CategorySRepository implements CategorySInterface
{
    public function getAll(): Collection
    {
        return Category::with('subcategories')->where('status', true)->get();
    }

    public function getById(int $id): Model
    {
        /* return Category::select('id', 'name')->findOrFail($id); */
        return Category::with('subcategories')->findOrFail($id);
    }
}
