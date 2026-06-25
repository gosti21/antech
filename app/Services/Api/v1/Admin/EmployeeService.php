<?php

namespace App\Services\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\EmployeeInterface;
use App\Exceptions\Api\v1\NotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;

/**
 * @extends BaseService<EmployeeInterface>
 */
class EmployeeService extends BaseService
{
    public function __construct(EmployeeInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $userData = Arr::only($data, [
            'name',
            'last_name',
            'email',
            'password',
            'date_birth',
        ]);
        $employeeData = Arr::only($data, [
            'salary',
            'position',
        ]);
        $phoneData = Arr::only($data, [
            'phone',
        ]);
        $documentData = Arr::only($data, [
            'document_number',
            'document_type',
        ]);

        return $this->repository->create($userData, $employeeData, $phoneData, $documentData);
    }

    public function update(array $data, int $id): Model
    {
        try {
            $userData = Arr::only($data, [
                'name',
                'last_name',
                'email',
                'password',
                'date_birth',
            ]);
            $employeeData = Arr::only($data, [
                'salary',
                'position',
                'status',
            ]);
            $phoneData = Arr::only($data, [
                'phone',
            ]);
            $documentData = Arr::only($data, [
                'document_number',
                'document_type',
            ]);
            return $this->repository->update($id, $userData, $employeeData, $phoneData, $documentData);
        } catch (ModelNotFoundException) {
            throw new NotFoundException();
        }
    }
}
