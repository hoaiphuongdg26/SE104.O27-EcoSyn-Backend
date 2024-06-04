<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first() ?? Role::create(['name' => 'admin']);
        $userRole = Role::where('name', 'user')->first() ?? Role::create(['name' => 'user']);

        //Full permission for admin
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

        //Permission for user
        $userRole->givePermissionTo('view own user');
    }
}
