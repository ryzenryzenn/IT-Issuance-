<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view assets', 'create assets', 'update assets', 'delete assets', 'transfer assets',
            'view companies', 'create companies', 'update companies', 'delete companies',
            'view categories', 'create categories', 'update categories', 'delete categories',
            'view locations', 'create locations', 'update locations', 'delete locations',
            'view asset models', 'create asset models', 'update asset models', 'delete asset models',
            'view employees', 'create employees', 'update employees', 'delete employees',
            'view users', 'create users', 'update users', 'delete users',
            'view audit logs',
            'upload accountability files', 'delete accountability files',
            'export reports',
            'view trash', 'restore records', 'force delete records',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $admin   = Role::firstOrCreate(['name' => 'Admin',    'guard_name' => 'web']);
        $itStaff = Role::firstOrCreate(['name' => 'IT Staff', 'guard_name' => 'web']);
        $viewer  = Role::firstOrCreate(['name' => 'Viewer',   'guard_name' => 'web']);

        $admin->syncPermissions(Permission::all());

        $itStaff->syncPermissions([
            'view assets', 'create assets', 'update assets', 'delete assets', 'transfer assets',
            'view companies', 'create companies', 'update companies',
            'view categories', 'create categories', 'update categories',
            'view locations', 'create locations', 'update locations',
            'view asset models', 'create asset models', 'update asset models',
            'view employees', 'create employees', 'update employees',
            'view audit logs',
            'upload accountability files', 'delete accountability files',
            'export reports',
        ]);

        $viewer->syncPermissions([
            'view assets', 'view companies', 'view categories',
            'view locations', 'view asset models', 'view employees', 'export reports',
        ]);
    }
}
