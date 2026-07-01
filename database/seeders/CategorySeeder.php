<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Desktop',     'description' => 'Desktop computers and workstations'],
            ['name' => 'Laptop',      'description' => 'Portable laptops'],
            ['name' => 'Monitor',     'description' => 'External monitors and displays'],
            ['name' => 'Printer',     'description' => 'Inkjet, laser, and dot-matrix printers'],
            ['name' => 'Networking',  'description' => 'Routers, switches, access points'],
            ['name' => 'Peripherals', 'description' => 'Keyboards, mice, headsets, webcams'],
            ['name' => 'Mobile',      'description' => 'Smartphones and tablets'],
            ['name' => 'Other',       'description' => 'Miscellaneous IT assets'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(['name' => $c['name']], $c);
        }
    }
}
