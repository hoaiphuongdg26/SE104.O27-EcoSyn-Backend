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
        $roles = ['customer ', 'staff', 'admin', 'super admin'];
        $this->createRoles($roles);

        //Full permission for admin
        $adminRole = Role::where('name', 'admin')->first();
        $permissions = Permission::all();
        $adminRole->syncPermissions($permissions);

    }
    public function createRoles(array $roles): void
    {
        foreach ($roles as $roleName) {
            // Tìm kiếm vai trò với tên đã cho, nếu không có thì tạo mới
            $role = Role::where('name', $roleName)->first() ?? Role::create(['name' => $roleName]);
        }
    }
}
