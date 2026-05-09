<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesData = [
            [
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Author',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Reader',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Author & Reader',
                'guard_name' => 'web',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]
        ];

        foreach($rolesData as $key => $value)
        {
            $isRoleExists = Role::where('name', $value['name'])->first();

            if(empty($isRoleExists))
            {
                $role = Role::create($value);

                if($value['name'] == 'Admin')
                {
                    $permissions = Permission::get();
                    foreach($permissions as $permissionKey => $permissionValue){
                        if(!($role->hasPermissionTo($permissionValue['name']))){
                            $role->givePermissionTo($permissionValue['name']);
                        }
                    }
                }
            }
            else
            {
                if($value['name'] == 'Admin')
                {
                    $permissions = Permission::get();
                    foreach($permissions as $permissionKey => $permissionValue){
                        if(!($isRoleExists->hasPermissionTo($permissionValue['name']))){
                            $isRoleExists->givePermissionTo($permissionValue['name']);
                        }
                    }
                }
            }
        }
    }
}
