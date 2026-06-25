<?php

namespace App\Models;

use App\traits\OrderNumberGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use OrderNumberGenerator;

    protected $fillable = [
        'order_number',
        'type_sale',
        'subtotal',
        'igv',
        'shipment_cost',
        'total_discount',
        'total',
        'status',
        'payment_status',
        'employee_id',
        'branch_id',
        'user_id',
        'customer_id',
        'cart_id',
        'checkout_snapshot',
    ];

    protected $casts = [
        'checkout_snapshot' => 'array',
    ];


    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function paymentMethods(): BelongsToMany
    {
        return $this->belongsToMany(PaymentMethod::class, 'order_payment_method', 'order_id', 'payment_method_id')
            ->using(OrderPaymentMethod::class)->withPivot('id', 'amount', 'transaction_id')->withTimestamps();
    }

    public function branchVariants(): BelongsToMany
    {
        return $this->belongsToMany(BranchVariant::class, 'order_detail', 'order_id', 'branch_variant_id')
            ->using(OrderDetail::class)->withPivot('id', 'product_name', 'variant_sku', 'unit_price', 'discount', 'quantity', 'subtotal')->withTimestamps();
    }

    public function shipment(): HasOne //puede ser hasmany
    {
        return $this->HasOne(Shipment::class);
    }

    public function voucher(): HasOne
    {
        return $this->hasOne(Voucher::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    #relacion de ayuda
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
