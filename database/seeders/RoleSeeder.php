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
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $leader = Role::create(['name' => 'leader']);
        $developer = Role::create(['name' => 'developer']);
        $admin = Role::create(['name' => 'admin']);

        // Leader Permissions
        $leaderPermissions = [
            'create team',
            'delete team',
            'add team member',
            'remove team member',
            'assign member permissions',
            'revoke member permissions',
            'create project',
            'start project',
            'complete project',
            'update project',
            'delete project',
            'create task',
            'assign task',
            'unassign task',
            'delete task',
            'download task files',
            'approve task',
            'reject task',
            'add comment on task review',
        ];

        $developerPermissions = [
            'update task status',
            'upload task files',
            'add report',
        ];

        // Admin gets all permissions
        $adminPermissions = Permission::pluck('name')->toArray();

        // Assign permissions to roles
        $leader->givePermissionTo($leaderPermissions);
        $developer->givePermissionTo($developerPermissions);
        $admin->givePermissionTo($adminPermissions);

        // Additional roles if needed
        // $manager = Role::create(['name' => 'manager']);
        // $manager->givePermissionTo([...]);
    }
}
