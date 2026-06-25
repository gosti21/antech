<?php

namespace App\Repositories\Api\v1\Admin;

use App\Contracts\Api\v1\Admin\EmployeeInterface;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeRepository extends BaseRepository implements EmployeeInterface
{
    public function __construct(Employee $model)
    {
        parent::__construct($model);
    }

    public function getAll(int $pagination): LengthAwarePaginator
    {
        return $this->model::with(['user', 'phone', 'documentNumber'])->paginate($pagination);
    }

    public function getById(int $id): Model
    {
        return $this->model::with(['user', 'phone', 'documentNumber'])->findOrFail($id);
    }

    public function create(array $userData, array $employeeData, array $phoneData, array $documentData): Model
    {
        return DB::transaction(function () use (
            $userData,
            $employeeData,
            $phoneData,
            $documentData
        ) {
            $user = User::create([
                'name' => $userData['name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'date_birth' => $userData['date_birth'] ?? null,
            ]);

            $employee = $user->employee()->create([
                'salary' => $employeeData['salary'],
                'position' => $employeeData['position'],
                'branch_id' => 1
            ]);

            $employee->phone()->create([
                'number' => $phoneData['phone'],
                'prefix_id' => 1,
            ]);

            $documentTypeId = DocumentType::where('type', $documentData['document_type'])
                ->firstOrFail()
                ->id;

            $employee->documentNumber()->create([
                'number' => $documentData['document_number'],
                'document_type_id' => $documentTypeId,
            ]);

            $user->assignRole('employee');

            return $employee->refresh()->load(['user', 'phone', 'documentNumber']);
        });
    }

    public function update(int $id, array $userData, array $employeeData, array $phoneData, array $documentData): Model
    {
        $employee = $this->getById($id);

        return DB::transaction(function () use (
            $employee,
            $userData,
            $employeeData,
            $phoneData,
            $documentData
        ) {
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }
            $employee->user()->update($userData);

            $employee->update($employeeData);

            if(isset($phoneData['phone'])){
                $employee->phone()->update([
                    'number' => $phoneData['phone'],
                ]);
            }

            $updateData = [];

            if (isset($documentData['document_number'])) {
                $updateData['number'] = $documentData['document_number'];
            }

            if (isset($documentData['document_type'])) {
                $documentTypeId = DocumentType::where('type', $documentData['document_type'])
                    ->firstOrFail()
                    ->id;

                $updateData['document_type_id'] = $documentTypeId;
            }

            if (!empty($updateData)) {
                $employee->documentNumber()->update($updateData);
            }

            return $employee->refresh()->load(['user', 'phone', 'documentNumber']);
        });
        $model->update();
        return $model->refresh();
    }
}
