<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Admin Panel
            'access-dashboard',
            'manage-categories',
            'manage-subcategories',
            'manage-brands',
            'manage-products',
            'manage-covers',
            'manage-specifications',
            'manage-options',
            'manage-branches',
            'manage-variants',
            'manage-countries',
            'manage-departments',
            'manage-provinces',
            'manage-districts',
            'manage-movements',
            'manage-option-values',
            'manage-option-products',
            'manage-variant-barcodes',
            'manage-branch-variants',
            'manage-users',
            'manage-employees',

            // Mobile App
            'access-m-app',
            'access-m-categories',
            'access-m-brands',
            'access-m-variants',
            'process-m-cart',
            'access-m-products',

            // Shop
            'access-s-shop',
            'access-s-cart-merge',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos

        // ROL: Customer (Usuario de tienda virtual)
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'access-s-shop',
            'access-s-cart-merge',
        ]);

        // ROL: Employee (Empleado - solo app móvil)
        $employeeRole = Role::firstOrCreate(['name' => 'employee']);
        $employeeRole->syncPermissions([
            'access-m-app',
            'access-m-categories',
            'access-m-brands',
            'access-m-variants',
            'process-m-cart',
            'access-m-products',
            'access-s-shop', // Los empleados también pueden comprar
            'access-s-cart-merge',
        ]);

        // ROL: Admin (Acceso total)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $user = User::find(1);
        if ($user) {
            $user->syncRoles(['admin']);
        }
    }
}
