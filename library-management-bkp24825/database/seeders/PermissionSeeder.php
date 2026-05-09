<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsData = [
            [
                'name' => 'View Role',
                'guard_name' => 'web',
                'slug' => 'view-role',
                'module_name' => 'Role',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Role',
                'guard_name' => 'web',
                'slug' => 'add-role',
                'module_name' => 'Role',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Role',
                'guard_name' => 'web',
                'slug' => 'edit-role',
                'module_name' => 'Role',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Role',
                'guard_name' => 'web',
                'slug' => 'delete-role',
                'module_name' => 'Role',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'View Dashboard',
                'guard_name' => 'web',
                'slug' => 'view-dashboard',
                'module_name' => 'Dashboard',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Dashboard',
                'guard_name' => 'web',
                'slug' => 'add-dashboard',
                'module_name' => 'Dashboard',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Dashboard',
                'guard_name' => 'web',
                'slug' => 'edit-dashboard',
                'module_name' => 'Dashboard',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Dashboard',
                'guard_name' => 'web',
                'slug' => 'delete-dashboard',
                'module_name' => 'Dashboard',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'View User',
                'guard_name' => 'web',
                'slug' => 'view-user',
                'module_name' => 'User',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add User',
                'guard_name' => 'web',
                'slug' => 'add-user',
                'module_name' => 'User',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit User',
                'guard_name' => 'web',
                'slug' => 'edit-user',
                'module_name' => 'User',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete User',
                'guard_name' => 'web',
                'slug' => 'delete-user',
                'module_name' => 'User',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'View Book',
                'guard_name' => 'web',
                'slug' => 'view-book',
                'module_name' => 'Book',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Book',
                'guard_name' => 'web',
                'slug' => 'add-book',
                'module_name' => 'Book',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Book',
                'guard_name' => 'web',
                'slug' => 'edit-book',
                'module_name' => 'Book',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Book',
                'guard_name' => 'web',
                'slug' => 'delete-book',
                'module_name' => 'Book',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
        ];

        foreach($permissionsData as $key => $value)
        {
            $isPermissionExists = Permission::where('name', $value['name'])->exists();

            if(!$isPermissionExists)
            {
                Permission::create($value);
            }
        }
    }
}
