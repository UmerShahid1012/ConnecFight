<?php

namespace Database\Seeders;

use App\Models\DisputeList;
use App\Models\Plan;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        $this->call(TagSeedr::class);
        $this->call(StanceSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(PlanSeeder::class);
        $this->call(FacilitySeeder::class);
        $this->call(DisputeListSeeder::class);

    }
}
