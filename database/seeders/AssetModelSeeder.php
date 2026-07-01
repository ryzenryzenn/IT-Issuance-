<?php

namespace Database\Seeders;

use App\Models\AssetModel;
use Illuminate\Database\Seeder;

class AssetModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            ['name' => 'Dell Latitude 5440',  'description' => 'Business laptop'],
            ['name' => 'HP ProDesk 400 G9',   'description' => 'Desktop workstation'],
            ['name' => 'Lenovo ThinkPad E14',  'description' => 'Business laptop'],
            ['name' => 'Epson L3250',          'description' => 'Ink tank printer'],
            ['name' => 'LG 24MK430H 24"',      'description' => '24-inch monitor'],
        ];

        foreach ($models as $m) {
            AssetModel::firstOrCreate(['name' => $m['name']], $m);
        }
    }
}
