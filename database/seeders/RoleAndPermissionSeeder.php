<?php

namespace Database\Seeders;

use App\Enums\Permission as Perm;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Perm::values() as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $admin   = Role::firstOrCreate(['name' => 'Admin',    'guard_name' => 'web']);
        $itStaff = Role::firstOrCreate(['name' => 'IT Staff', 'guard_name' => 'web']);
        $viewer  = Role::firstOrCreate(['name' => 'Viewer',   'guard_name' => 'web']);

        // Admin: everything.
        $admin->syncPermissions(Permission::all());

        // IT Staff: manage the asset workflow, but not users/roles/trash or destructive deletes on reference data.
        $itStaff->syncPermissions(Perm::names([
            Perm::ViewAssets, Perm::CreateAssets, Perm::UpdateAssets, Perm::DeleteAssets, Perm::TransferAssets,
            Perm::ViewCompanies, Perm::CreateCompanies, Perm::UpdateCompanies,
            Perm::ViewCategories, Perm::CreateCategories, Perm::UpdateCategories,
            Perm::ViewLocations, Perm::CreateLocations, Perm::UpdateLocations,
            Perm::ViewAssetModels, Perm::CreateAssetModels, Perm::UpdateAssetModels,
            Perm::ViewEmployees, Perm::CreateEmployees, Perm::UpdateEmployees,
            Perm::ViewTickets, Perm::CreateTickets, Perm::UpdateTickets, Perm::DeleteTickets,
            Perm::ViewAuditLogs,
            Perm::UploadAccountabilityFiles, Perm::DeleteAccountabilityFiles,
            Perm::ExportReports,
        ]));

        // Viewer: read-only + exports.
        $viewer->syncPermissions(Perm::names([
            Perm::ViewAssets, Perm::ViewCompanies, Perm::ViewCategories,
            Perm::ViewLocations, Perm::ViewAssetModels, Perm::ViewEmployees,
            Perm::ViewTickets,
            Perm::ExportReports,
        ]));
    }
}
