<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Extend permission logic to check group-based permissions
        Gate::before(function (User $user, string $permission) {
            if ($permission === 'view aircraft-types' || $permission === 'add aircraft-types' || $permission === 'edit aircraft-types' || $permission === 'delete aircraft-types') {
                // Allow access to aircraft types if the user has the permission or belongs to a group with the permission 'Aircrafts'
                $permission = str_replace('aircraft-types', 'aircrafts', $permission);
            }
            // If user has direct permission, allow
            if ($user->hasPermissionTo($permission)) {
                return true;
            }

            // If user's group has permission, allow
            return $user->group && $user->group->roles->flatMap->permissions->pluck('name')->contains($permission);
        });
    }
}
