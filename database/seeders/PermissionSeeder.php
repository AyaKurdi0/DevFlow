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
            // Team Management Permissions
            'create team',
            'delete team',
            'add team member',
            'remove team member',
            'assign member permissions',
            'revoke member permissions',

            // Project Management Permissions
            'create project',
            'start project',
            'complete project',
            'update project',
            'delete project',

            // Task Management Permissions (Leader)
            'create task',
            'assign task',
            'unassign task',
            'delete task',
            'download task files',

            // Review Management Permissions
            'approve task',
            'reject task',
            'add comment on task review',

            // Task Management Permissions (Developer)
            'update task status',
            'upload task files',

            // Report Management Permissions
            'add report',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
