<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $leader = Role::create(['name' => 'leader']);
//        $developer = Role::create(['name' => 'developer']);
//        $admin = Role::create(['name' => 'admin']);
//        $manager = Role::create(['name' => 'manager']);


        $leaderPermissions = [
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
        ];

        $developerPermissions = [
            'view own tasks',
            'update task status',
            'push code to repository',
            'upload documents',
            'submit task report',
            'view code reviews',
            'view completed tasks summary',
            'view task comments',
        ];

        $leaderRoles = Role::create(['name' => 'leader']);
        $leaderRoles->givePermissionTo($leaderPermissions);
        $developerRoles = Role::create(['name' => 'developer']);
        $developerRoles->givePermissionTo($developerPermissions);
    }
}
