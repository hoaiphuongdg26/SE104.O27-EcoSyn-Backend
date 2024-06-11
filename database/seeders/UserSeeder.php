<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name'         => 'Administrator',
            'email'        => 'admin@admin.com',
            'password'     => bcrypt('admin@12345')
        ]);
        $adminRole = Role::where('name', 'admin')->first() ?? Role::create(['name' => 'admin']);
        $user->assignRole($adminRole);

        $user2 = User::create([
            'name'         => 'Customer',
            'email'        => 'customer@gmail.com',
            'password'     => bcrypt('customer@12345')
        ]);
        $customerRole = Role::where('name', 'customer')->first() ?? Role::create(['name' => 'customer']);
        $user2->assignRole($customerRole);

        \App\Models\User::factory(5)->create();
    }
}
