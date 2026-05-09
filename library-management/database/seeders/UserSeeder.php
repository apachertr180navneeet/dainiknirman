<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersData = [
            [
                "name" => "Admin User",
                "email" => "admin@dainiknirman.com",
                "mobile" => "9876544321",
                "username" => "admin",
                "password" => Hash::make("123456"),
                "role_id" => 1, // Admin
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ]
        ];

        foreach ($usersData as $key => $value)
        {
            $isUserExists = User::where('email', $value['email'])->first();

            if(!$isUserExists)
            {
                $user = User::create($value);

                // Assign Role
                $user->assignRole('Admin');
            }
            else
            {
                $permissions = Permission::get();
                
                foreach($permissions as $permissionKey => $permissionValue){
                    if(!($isUserExists->hasPermissionTo($permissionValue['name']))){
                        $isUserExists->givePermissionTo($permissionValue['name']);
                    }
                }
            }
        }

        // User::factory()
        // ->count(10)
        // ->state(function () {
        //     $roleId = rand(2, 4);
        //     return ['role_id' => $roleId];
        // })
        // ->afterCreating(function ($user) {
        //     $role = Role::find($user->role_id);
        //     if ($role) {
        //         $user->assignRole($role->name);
        //     }
        // })
        // ->create();
    }
}
