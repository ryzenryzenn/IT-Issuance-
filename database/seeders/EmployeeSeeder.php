<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            ['name' => 'Juan Dela Cruz', 'department' => 'Finance',   'position' => 'Accountant'],
            ['name' => 'Maria Santos',   'department' => 'HR',        'position' => 'HR Officer'],
            ['name' => 'Pedro Reyes',    'department' => 'Logistics', 'position' => 'Warehouse Staff'],
            ['name' => 'Anna Lim',       'department' => 'Retail',    'position' => 'Store Supervisor'],
        ];

        foreach ($employees as $e) {
            Employee::firstOrCreate(['name' => $e['name']], $e + ['is_active' => true]);
        }
    }
}
