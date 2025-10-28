<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RbacSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // Add your seeding logic here
        
        // Run the RbacSeeder
        $this->call(RbacSeeder::class);  // This will execute the RbacSeeder

        // You can add any other seeders or logic you need after running RbacSeeder
    }
}
