<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Dashboard
        Permission::create(['name' => 'view dashboard', 'guard_name' => 'web']);

        // Search
        Permission::create(['name' => 'view search', 'guard_name' => 'web']);

        // Administration
        Permission::create(['name' => 'view administration', 'guard_name' => 'web']);
        // Administration - Roles
        Permission::create(['name' => 'view roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'add roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete roles', 'guard_name' => 'web']);
        // Administration - Groups
        Permission::create(['name' => 'view groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'add groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit groups', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete groups', 'guard_name' => 'web']);
        // Administration - Users
        Permission::create(['name' => 'view users', 'guard_name' => 'web']);
        Permission::create(['name' => 'add users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit users', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete users', 'guard_name' => 'web']);
        // Administration - BIOS Audit
        Permission::create(['name' => 'view bios-audit', 'guard_name' => 'web']);
    }
}
