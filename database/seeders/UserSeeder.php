<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::create([
            'name' => 'Admin AlSat',
            'email' => 'admin@alsat.az',
            'password' => '$2y$10$hcn0QuYc5NOiKrjaNMGNIeITHW3bzJ6UeTVWWg/1ZaFQ8eXX1Incm' //Password
        ]);
        $admin->givePermissionTo(Permission::all());
    }
}
