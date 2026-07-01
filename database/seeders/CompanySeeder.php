<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'MeatPlus Corporation', 'code' => 'MPC', 'address' => 'Metro Manila, Philippines'],
            ['name' => 'MeatPlus Logistics',   'code' => 'MPL', 'address' => 'Cavite, Philippines'],
            ['name' => 'MeatPlus Retail',      'code' => 'MPR', 'address' => 'Pampanga, Philippines'],
        ];

        foreach ($companies as $c) {
            Company::firstOrCreate(['code' => $c['code']], $c);
        }
    }
}
