<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $mpc = Company::where('code', 'MPC')->first();
        $mpl = Company::where('code', 'MPL')->first();
        $mpr = Company::where('code', 'MPR')->first();

        $cats = Category::all()->keyBy('name');

        $rows = [
            [
                'company_id' => $mpc->id, 'category_id' => $cats['Laptop']->id,
                'asset_tag'  => 'MPC-LT-0001', 'asset_model' => 'Dell Latitude 5440',
                'assigned_user' => 'Juan Dela Cruz', 'location' => 'Head Office - Finance',
                'rustdesk_id' => '123 456 789',
                'windows_license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-W11A1',
                'latest_updates_remarks' => 'Issued June 2026, antivirus updated.',
                'accountability_signed' => 'yes', 'accountability_uploaded_snipeit' => 'yes',
                'date_issued' => now()->subDays(20)->toDateString(),
            ],
            [
                'company_id' => $mpc->id, 'category_id' => $cats['Desktop']->id,
                'asset_tag'  => 'MPC-DT-0001', 'asset_model' => 'HP ProDesk 400 G9',
                'assigned_user' => 'Maria Santos', 'location' => 'Head Office - HR',
                'rustdesk_id' => '234 567 890',
                'windows_license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-W11A2',
                'latest_updates_remarks' => 'Windows 11 Pro reinstalled.',
                'accountability_signed' => 'pending', 'accountability_uploaded_snipeit' => 'pending',
                'date_issued' => now()->subDays(15)->toDateString(),
            ],
            [
                'company_id' => $mpl->id, 'category_id' => $cats['Laptop']->id,
                'asset_tag'  => 'MPL-LT-0001', 'asset_model' => 'Lenovo ThinkPad E14',
                'assigned_user' => 'Pedro Reyes', 'location' => 'Logistics - Warehouse',
                'rustdesk_id' => '345 678 901',
                'windows_license_key' => 'XXXXX-XXXXX-XXXXX-XXXXX-W11A3',
                'latest_updates_remarks' => 'BIOS firmware updated.',
                'accountability_signed' => 'yes', 'accountability_uploaded_snipeit' => 'pending',
                'date_issued' => now()->subDays(10)->toDateString(),
            ],
            [
                'company_id' => $mpr->id, 'category_id' => $cats['Printer']->id,
                'asset_tag'  => 'MPR-PR-0001', 'asset_model' => 'Epson L3250',
                'assigned_user' => 'Anna Lim', 'location' => 'Retail - Store 1',
                'rustdesk_id' => null,
                'windows_license_key' => null,
                'latest_updates_remarks' => 'Ink tank refilled.',
                'accountability_signed' => 'pending', 'accountability_uploaded_snipeit' => 'pending',
                'date_issued' => now()->subDays(5)->toDateString(),
            ],
            [
                'company_id' => $mpc->id, 'category_id' => $cats['Monitor']->id,
                'asset_tag'  => 'MPC-MN-0001', 'asset_model' => 'LG 24MK430H 24"',
                'assigned_user' => null, 'location' => 'IT Storage',
                'rustdesk_id' => null,
                'windows_license_key' => null,
                'latest_updates_remarks' => 'Spare monitor in IT storage.',
                'accountability_signed' => 'pending', 'accountability_uploaded_snipeit' => 'pending',
                'date_issued' => null,
            ],
        ];

        foreach ($rows as $row) {
            // Resolve the free-text model/location/assignee into managed records.
            $model    = AssetModel::firstOrCreate(['name' => $row['asset_model']]);
            $location = Location::firstOrCreate(['name' => $row['location']]);

            $row['model_id']    = $model->id;
            $row['location_id'] = $location->id;

            if (! empty($row['assigned_user'])) {
                $employee = Employee::firstOrCreate(['name' => $row['assigned_user']], ['is_active' => true]);
                $row['assignee_type'] = 'employee';
                $row['assignee_id']   = $employee->id;
            }

            unset($row['asset_model'], $row['location'], $row['assigned_user']);

            Asset::firstOrCreate(['asset_tag' => $row['asset_tag']], $row);
        }
    }
}
