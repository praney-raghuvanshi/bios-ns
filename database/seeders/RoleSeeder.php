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
    }
}
