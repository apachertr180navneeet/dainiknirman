<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear permissions cache
        Artisan::call('permission:cache-reset');

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
            // Magazines
            [
                'name' => 'View Magazines',
                'guard_name' => 'web',
                'slug' => 'view-magazines',
                'module_name' => 'Magazines',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Magazines',
                'guard_name' => 'web',
                'slug' => 'add-magazines',
                'module_name' => 'Magazines',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Magazines',
                'guard_name' => 'web',
                'slug' => 'edit-magazines',
                'module_name' => 'Magazines',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Magazines',
                'guard_name' => 'web',
                'slug' => 'delete-magazines',
                'module_name' => 'Magazines',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Cms
            [
                'name' => 'View Cms',
                'guard_name' => 'web',
                'slug' => 'view-cms',
                'module_name' => 'Cms',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Cms',
                'guard_name' => 'web',
                'slug' => 'add-cms',
                'module_name' => 'Cms',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Cms',
                'guard_name' => 'web',
                'slug' => 'edit-cms',
                'module_name' => 'Cms',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Cms',
                'guard_name' => 'web',
                'slug' => 'delete-cms',
                'module_name' => 'Cms',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Contest
            [
                'name' => 'View Contest',
                'guard_name' => 'web',
                'slug' => 'view-contest',
                'module_name' => 'Contest',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Contest',
                'guard_name' => 'web',
                'slug' => 'add-contest',
                'module_name' => 'Contest',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Contest',
                'guard_name' => 'web',
                'slug' => 'edit-contest',
                'module_name' => 'Contest',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Contest',
                'guard_name' => 'web',
                'slug' => 'delete-contest',
                'module_name' => 'Contest',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Contest Author
            [
                'name' => 'View Contest Author',
                'guard_name' => 'web',
                'slug' => 'view-contest-author',
                'module_name' => 'Contest Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Contest Author',
                'guard_name' => 'web',
                'slug' => 'add-contest-author',
                'module_name' => 'Contest Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Contest Author',
                'guard_name' => 'web',
                'slug' => 'edit-contest-author',
                'module_name' => 'Contest Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Contest Author',
                'guard_name' => 'web',
                'slug' => 'delete-contest-author',
                'module_name' => 'Contest Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Ebook upload Author
            [
                'name' => 'View Ebook Upload Author',
                'guard_name' => 'web',
                'slug' => 'view-ebook-upload-author',
                'module_name' => 'Ebook Upload Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Ebook Upload Author',
                'guard_name' => 'web',
                'slug' => 'add-ebook-upload-author',
                'module_name' => 'Ebook Upload Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Ebook Upload Author',
                'guard_name' => 'web',
                'slug' => 'edit-ebook-upload-author',
                'module_name' => 'Ebook Upload Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Ebook Upload Author',
                'guard_name' => 'web',
                'slug' => 'delete-ebook-upload-author',
                'module_name' => 'Ebook Upload Author',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Subscription
            [
                'name' => 'View Subscription',
                'guard_name' => 'web',
                'slug' => 'view-subscription',
                'module_name' => 'Subscription',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Subscription',
                'guard_name' => 'web',
                'slug' => 'add-subscription',
                'module_name' => 'Subscription',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Subscription',
                'guard_name' => 'web',
                'slug' => 'edit-subscription',
                'module_name' => 'Subscription',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Subscription',
                'guard_name' => 'web',
                'slug' => 'delete-subscription',
                'module_name' => 'Subscription',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Settings
            [
                'name' => 'View Setting',
                'guard_name' => 'web',
                'slug' => 'view-setting',
                'module_name' => 'Setting',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Setting',
                'guard_name' => 'web',
                'slug' => 'add-setting',
                'module_name' => 'Setting',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Setting',
                'guard_name' => 'web',
                'slug' => 'edit-setting',
                'module_name' => 'Setting',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Setting',
                'guard_name' => 'web',
                'slug' => 'delete-setting',
                'module_name' => 'Setting',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Royalty
            [
                'name' => 'View Royalty',
                'guard_name' => 'web',
                'slug' => 'view-royalty',
                'module_name' => 'Royalty',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Royalty',
                'guard_name' => 'web',
                'slug' => 'add-royalty',
                'module_name' => 'Royalty',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Royalty',
                'guard_name' => 'web',
                'slug' => 'edit-royalty',
                'module_name' => 'Royalty',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Royalty',
                'guard_name' => 'web',
                'slug' => 'delete-royalty',
                'module_name' => 'Royalty',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Royalty Calculation
            [
                'name' => 'View Royalty Calculation',
                'guard_name' => 'web',
                'slug' => 'view-royalty-calculation',
                'module_name' => 'Royalty Calculation',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Royalty Calculation',
                'guard_name' => 'web',
                'slug' => 'add-royalty-calculation',
                'module_name' => 'Royalty Calculation',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Royalty Calculation',
                'guard_name' => 'web',
                'slug' => 'edit-royalty-calculation',
                'module_name' => 'Royalty Calculation',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Royalty Calculation',
                'guard_name' => 'web',
                'slug' => 'delete-royalty-calculation',
                'module_name' => 'Royalty Calculation',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Anthology Writeup Mangement
            [
                'name' => 'View Anthology Writeup',
                'guard_name' => 'web',
                'slug' => 'view-anthology-writeup',
                'module_name' => 'Anthology Writeup',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Anthology Writeup',
                'guard_name' => 'web',
                'slug' => 'add-anthology-writeup',
                'module_name' => 'Anthology Writeup',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Anthology Writeup',
                'guard_name' => 'web',
                'slug' => 'edit-anthology-writeup',
                'module_name' => 'Anthology Writeup',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Anthology Writeup',
                'guard_name' => 'web',
                'slug' => 'delete-anthology-writeup',
                'module_name' => 'Anthology Writeup',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Payment Mangement
            [
                'name' => 'View Payment Management',
                'guard_name' => 'web',
                'slug' => 'view-payment-management',
                'module_name' => 'Payment Management',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Payment Management',
                'guard_name' => 'web',
                'slug' => 'add-payment-management',
                'module_name' => 'Payment Management',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Payment Management',
                'guard_name' => 'web',
                'slug' => 'edit-payment-management',
                'module_name' => 'Payment Management',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Payment Management',
                'guard_name' => 'web',
                'slug' => 'delete-payment-management',
                'module_name' => 'Payment Management',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Orders Mangement
            [
                'name' => 'View Orders',
                'guard_name' => 'web',
                'slug' => 'view-orders',
                'module_name' => 'Orders',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Orders',
                'guard_name' => 'web',
                'slug' => 'add-orders',
                'module_name' => 'Orders',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Orders',
                'guard_name' => 'web',
                'slug' => 'edit-orders',
                'module_name' => 'Orders',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Orders',
                'guard_name' => 'web',
                'slug' => 'delete-orders',
                'module_name' => 'Orders',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Notification Mangement
            [
                'name' => 'View Notification',
                'guard_name' => 'web',
                'slug' => 'view-notification',
                'module_name' => 'Notification',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Notification',
                'guard_name' => 'web',
                'slug' => 'add-notification',
                'module_name' => 'Notification',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Notification',
                'guard_name' => 'web',
                'slug' => 'edit-notification',
                'module_name' => 'Notification',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Notification',
                'guard_name' => 'web',
                'slug' => 'delete-notification',
                'module_name' => 'Notification',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            // Anthology Management
            [
                'name' => 'View Anthology',
                'guard_name' => 'web',
                'slug' => 'view-anthology',
                'module_name' => 'Anthology',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Add Anthology',
                'guard_name' => 'web',
                'slug' => 'add-anthology',
                'module_name' => 'Anthology',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Edit Anthology',
                'guard_name' => 'web',
                'slug' => 'edit-anthology',
                'module_name' => 'Anthology',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'name' => 'Delete Anthology',
                'guard_name' => 'web',
                'slug' => 'delete-anthology',
                'module_name' => 'Anthology',
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
