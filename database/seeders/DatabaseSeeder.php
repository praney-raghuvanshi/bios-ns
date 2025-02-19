<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        $user = User::create([
            'name' => 'Piers Drury',
            'username' => 'piers.ns',
            'email' => 'piers-ns@bww.com',
            'password' => bcrypt('Piers@123#')
        ]);

        $this->call(GroupSeeder::class);

        $group = Group::find(1);

        // Assign roles to group
        if ($group) {
            $roleIds = Role::whereIn('name', ['dashboard-view', 'search-view', 'administration-view', 'administration-roles-admin', 'administration-groups-admin', 'administration-users-admin', 'administration-bios-audit-view'])->pluck('id');
            $group->roles()->sync($roleIds); // Assign roles to group
        }

        // Assign user to group
        $user->update([
            'group_id' => $group->id
        ]);
    }
}
