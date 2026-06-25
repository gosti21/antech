<?php

namespace App\Repositories\Api\v1\Mobile;

use App\Contracts\Api\v1\Mobile\VoucherMInterface;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Model;

class VoucherMRepository implements VoucherMInterface
{
    public function getById(int $id): Model
    {
        return Voucher::select('voucher_number', 'path')
            ->where('id', $id)
            ->firstOrFail();
    }
}
