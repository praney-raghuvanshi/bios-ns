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

        // Maintenance
        Permission::create(['name' => 'view maintenance', 'guard_name' => 'web']);
        // Maintenance - Zones
        Permission::create(['name' => 'view zones', 'guard_name' => 'web']);
        Permission::create(['name' => 'add zones', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit zones', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete zones', 'guard_name' => 'web']);
        // Maintenance - Locations
        Permission::create(['name' => 'view locations', 'guard_name' => 'web']);
        Permission::create(['name' => 'add locations', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit locations', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete locations', 'guard_name' => 'web']);
        // Maintenance - Airports
        Permission::create(['name' => 'view airports', 'guard_name' => 'web']);
        Permission::create(['name' => 'add airports', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit airports', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete airports', 'guard_name' => 'web']);
        // Maintenance - Products
        Permission::create(['name' => 'view products', 'guard_name' => 'web']);
        Permission::create(['name' => 'add products', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit products', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete products', 'guard_name' => 'web']);
        // Maintenance - Customers
        Permission::create(['name' => 'view customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'add customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit customers', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete customers', 'guard_name' => 'web']);
        // Maintenance - Operational Calendar
        Permission::create(['name' => 'view operational-calendars', 'guard_name' => 'web']);
        Permission::create(['name' => 'add operational-calendars', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit operational-calendars', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete operational-calendars', 'guard_name' => 'web']);
        // Maintenance - Aircrafts
        Permission::create(['name' => 'view aircrafts', 'guard_name' => 'web']);
        Permission::create(['name' => 'add aircrafts', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit aircrafts', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete aircrafts', 'guard_name' => 'web']);
        // Maintenance - Flights
        Permission::create(['name' => 'view flights', 'guard_name' => 'web']);
        Permission::create(['name' => 'add flights', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit flights', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete flights', 'guard_name' => 'web']);

        // Flight Operations
        Permission::create(['name' => 'view flight-operations', 'guard_name' => 'web']);
        // Flight Operations - Schedules
        Permission::create(['name' => 'view schedules', 'guard_name' => 'web']);
        Permission::create(['name' => 'add schedules', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit schedules', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete schedules', 'guard_name' => 'web']);
    }
}
