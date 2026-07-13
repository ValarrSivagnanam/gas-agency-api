<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Cylinder;

class CylinderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add default domestic gas cylinders
        Cylinder::updateOrCreate(
            ['type' => 'Domestic (14.2kg)'],
            ['full_stock' => 120, 'empty_stock' => 15, 'price' => 850.00]
        );

        // Add default commercial gas cylinders
        Cylinder::updateOrCreate(
            ['type' => 'Commercial (19kg)'],
            ['full_stock' => 45, 'empty_stock' => 8, 'price' => 1600.00]
        );
    }
}
