<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new Admin();
        $admin->name = 'Super Admin';
        $admin->email = 'admin@cnf.com';
        $admin->password = \Illuminate\Support\Facades\Hash::make(123456);
        $admin->role = 'super-admin';
        $admin->save();
    }
}
