<?php

namespace App\Filters\Api\v1\Admin\Customer;

use Closure;

class TypeCustomerFilter
{
    protected array $allowedTypes = ['people', 'company'];

    public function handle($query, Closure $next)
    {
        $typeCustomer = request('type_customer', 'people');

        // Validar que sea un valor permitido
        if (!in_array($typeCustomer, $this->allowedTypes)) {
            $typeCustomer = 'people';
        }

        $query->where('type_customer', $typeCustomer);

        return $next($query);
    }
}
