<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dashboard
        $roleDashboardView = Role::create(['name' => 'dashboard-view', 'guard_name' => 'web']);
        $roleDashboardView->givePermissionTo('view dashboard');

        // Search
        $roleSearchView = Role::create(['name' => 'search-view', 'guard_name' => 'web']);
        $roleSearchView->givePermissionTo('view search');

        // Administration
        $roleAdministrationView = Role::create(['name' => 'administration-view', 'guard_name' => 'web']);
        $roleAdministrationView->givePermissionTo('view administration');

        // Administration - Roles
        $roleAdministrationRolesView = Role::create(['name' => 'administration-roles-view', 'guard_name' => 'web']);
        $roleAdministrationRolesUser = Role::create(['name' => 'administration-roles-user', 'guard_name' => 'web']);
        $roleAdministrationRolesManager = Role::create(['name' => 'administration-roles-manager', 'guard_name' => 'web']);
        $roleAdministrationRolesAdmin = Role::create(['name' => 'administration-roles-admin', 'guard_name' => 'web']);

        $roleAdministrationRolesView->givePermissionTo('view roles');
        $roleAdministrationRolesUser->givePermissionTo(['view roles', 'add roles', 'edit roles']);
        $roleAdministrationRolesManager->givePermissionTo(['view roles', 'add roles', 'edit roles', 'delete roles']);
        $roleAdministrationRolesAdmin->givePermissionTo(['view roles', 'add roles', 'edit roles', 'delete roles']);

        // Administration - Groups
        $roleAdministrationGroupsView = Role::create(['name' => 'administration-groups-view', 'guard_name' => 'web']);
        $roleAdministrationGroupsUser = Role::create(['name' => 'administration-groups-user', 'guard_name' => 'web']);
        $roleAdministrationGroupsManager = Role::create(['name' => 'administration-groups-manager', 'guard_name' => 'web']);
        $roleAdministrationGroupsAdmin = Role::create(['name' => 'administration-groups-admin', 'guard_name' => 'web']);

        $roleAdministrationGroupsView->givePermissionTo('view groups');
        $roleAdministrationGroupsUser->givePermissionTo(['view groups', 'add groups', 'edit groups']);
        $roleAdministrationGroupsManager->givePermissionTo(['view groups', 'add groups', 'edit groups', 'delete groups']);
        $roleAdministrationGroupsAdmin->givePermissionTo(['view groups', 'add groups', 'edit groups', 'delete groups']);

        // Administration - Users
        $roleAdministrationUsersView = Role::create(['name' => 'administration-users-view', 'guard_name' => 'web']);
        $roleAdministrationUsersUser = Role::create(['name' => 'administration-users-user', 'guard_name' => 'web']);
        $roleAdministrationUsersManager = Role::create(['name' => 'administration-users-manager', 'guard_name' => 'web']);
        $roleAdministrationUsersAdmin = Role::create(['name' => 'administration-users-admin', 'guard_name' => 'web']);

        $roleAdministrationUsersView->givePermissionTo('view users');
        $roleAdministrationUsersUser->givePermissionTo(['view users', 'add users', 'edit users']);
        $roleAdministrationUsersManager->givePermissionTo(['view users', 'add users', 'edit users', 'delete users']);
        $roleAdministrationUsersAdmin->givePermissionTo(['view users', 'add users', 'edit users', 'delete users']);

        // Administration - BIOS Audit
        $roleAdministrationBiosAuditView = Role::create(['name' => 'administration-bios-audit-view', 'guard_name' => 'web']);
        $roleAdministrationBiosAuditView->givePermissionTo('view bios-audit');

        // Maintenance
        $roleMaintenanceView = Role::create(['name' => 'maintenance-view', 'guard_name' => 'web']);
        $roleMaintenanceView->givePermissionTo('view maintenance');

        // Maintenance - Zones
        $roleMaintenanceZonesView = Role::create(['name' => 'maintenance-zones-view', 'guard_name' => 'web']);
        $roleMaintenanceZonesUser = Role::create(['name' => 'maintenance-zones-user', 'guard_name' => 'web']);
        $roleMaintenanceZonesManager = Role::create(['name' => 'maintenance-zones-manager', 'guard_name' => 'web']);
        $roleMaintenanceZonesAdmin = Role::create(['name' => 'maintenance-zones-admin', 'guard_name' => 'web']);

        $roleMaintenanceZonesView->givePermissionTo('view zones');
        $roleMaintenanceZonesUser->givePermissionTo(['view zones', 'add zones', 'edit zones']);
        $roleMaintenanceZonesManager->givePermissionTo(['view zones', 'add zones', 'edit zones', 'delete zones']);
        $roleMaintenanceZonesAdmin->givePermissionTo(['view zones', 'add zones', 'edit zones', 'delete zones']);

        // Maintenance - Locations
        $roleMaintenanceLocationsView = Role::create(['name' => 'maintenance-locations-view', 'guard_name' => 'web']);
        $roleMaintenanceLocationsUser = Role::create(['name' => 'maintenance-locations-user', 'guard_name' => 'web']);
        $roleMaintenanceLocationsManager = Role::create(['name' => 'maintenance-locations-manager', 'guard_name' => 'web']);
        $roleMaintenanceLocationsAdmin = Role::create(['name' => 'maintenance-locations-admin', 'guard_name' => 'web']);

        $roleMaintenanceLocationsView->givePermissionTo('view locations');
        $roleMaintenanceLocationsUser->givePermissionTo(['view locations', 'add locations', 'edit locations']);
        $roleMaintenanceLocationsManager->givePermissionTo(['view locations', 'add locations', 'edit locations', 'delete locations']);
        $roleMaintenanceLocationsAdmin->givePermissionTo(['view locations', 'add locations', 'edit locations', 'delete locations']);

        // Maintenance - Airports
        $roleMaintenanceAirportsView = Role::create(['name' => 'maintenance-airports-view', 'guard_name' => 'web']);
        $roleMaintenanceAirportsUser = Role::create(['name' => 'maintenance-airports-user', 'guard_name' => 'web']);
        $roleMaintenanceAirportsManager = Role::create(['name' => 'maintenance-airports-manager', 'guard_name' => 'web']);
        $roleMaintenanceAirportsAdmin = Role::create(['name' => 'maintenance-airports-admin', 'guard_name' => 'web']);

        $roleMaintenanceAirportsView->givePermissionTo('view airports');
        $roleMaintenanceAirportsUser->givePermissionTo(['view airports', 'add airports', 'edit airports']);
        $roleMaintenanceAirportsManager->givePermissionTo(['view airports', 'add airports', 'edit airports', 'delete airports']);
        $roleMaintenanceAirportsAdmin->givePermissionTo(['view airports', 'add airports', 'edit airports', 'delete airports']);

        // Maintenance - Products
        $roleMaintenanceProductsView = Role::create(['name' => 'maintenance-products-view', 'guard_name' => 'web']);
        $roleMaintenanceProductsUser = Role::create(['name' => 'maintenance-products-user', 'guard_name' => 'web']);
        $roleMaintenanceProductsManager = Role::create(['name' => 'maintenance-products-manager', 'guard_name' => 'web']);
        $roleMaintenanceProductsAdmin = Role::create(['name' => 'maintenance-products-admin', 'guard_name' => 'web']);

        $roleMaintenanceProductsView->givePermissionTo('view products');
        $roleMaintenanceProductsUser->givePermissionTo(['view products', 'add products', 'edit products']);
        $roleMaintenanceProductsManager->givePermissionTo(['view products', 'add products', 'edit products', 'delete products']);
        $roleMaintenanceProductsAdmin->givePermissionTo(['view products', 'add products', 'edit products', 'delete products']);

        // Maintenance - Customers
        $roleMaintenanceCustomersView = Role::create(['name' => 'maintenance-customers-view', 'guard_name' => 'web']);
        $roleMaintenanceCustomersUser = Role::create(['name' => 'maintenance-customers-user', 'guard_name' => 'web']);
        $roleMaintenanceCustomersManager = Role::create(['name' => 'maintenance-customers-manager', 'guard_name' => 'web']);
        $roleMaintenanceCustomersAdmin = Role::create(['name' => 'maintenance-customers-admin', 'guard_name' => 'web']);

        $roleMaintenanceCustomersView->givePermissionTo('view customers');
        $roleMaintenanceCustomersUser->givePermissionTo(['view customers', 'add customers', 'edit customers']);
        $roleMaintenanceCustomersManager->givePermissionTo(['view customers', 'add customers', 'edit customers', 'delete customers']);
        $roleMaintenanceCustomersAdmin->givePermissionTo(['view customers', 'add customers', 'edit customers', 'delete customers']);
    }
}
