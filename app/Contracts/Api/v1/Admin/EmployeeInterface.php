<?php

namespace App\Contracts\Api\v1\Admin;

use Illuminate\Database\Eloquent\Model;

interface EmployeeInterface extends BaseInterface
{
    public function create(array $userData, array $employeeData, array $phoneData, array $documentData): Model;
    public function update(int $id, array $userData, array $employeeData, array $phoneData, array $documentData): Model;
}
