<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'add team member',
            'remove team member',
            'manage member permissions',
            'add project tasks',
            'manage project tasks',
            'view project dashboard',
            'review submitted tasks',
            'add task comments',
            'reassign tasks',
            'assign tasks',
            'view project stats',
            'reassign project stats',
            'monitor deployment',
            'view team performance reports',
            'send notification',

            'view own tasks',
            'update task status',
            'push code to repository',
            'upload documents',
            'submit task report',
            'view code reviews',
            'view completed tasks summary',
            'view task comments',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
