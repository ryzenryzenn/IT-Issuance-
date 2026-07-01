<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['name' => 'Head Office - Finance',   'description' => 'Finance department, head office'],
            ['name' => 'Head Office - HR',        'description' => 'Human resources, head office'],
            ['name' => 'Logistics - Warehouse',   'description' => 'Main logistics warehouse'],
            ['name' => 'Retail - Store 1',        'description' => 'Retail branch store 1'],
            ['name' => 'IT Storage',              'description' => 'IT spare equipment storage'],
        ];

        foreach ($locations as $l) {
            Location::firstOrCreate(['name' => $l['name']], $l);
        }
    }
}
