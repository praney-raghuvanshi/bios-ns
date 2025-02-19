<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'username', 'password', 'profile_image', 'active', 'last_login_at', 'group_id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('User')
            ->logAll()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}")
            ->logOnlyDirty(true)
            ->dontSubmitEmptyLogs();
    }

    public function getFormattedIdAttribute()
    {
        return '#' . Str::padLeft($this->attributes['id'], 6, '0');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', 1);
    }

    /** Relationships */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getRolesFromGroup()
    {
        return $this->group ? $this->group->roles->pluck('name') : collect();
    }

    public function hasGroupRole($role)
    {
        return $this->getRolesFromGroup()->contains($role);
    }

    public function favourites()
    {
        return $this->hasMany(UserFavouriteMenu::class, 'user_id')->pluck('menu_item_slug')->toArray();
    }

    public function favouritesDropdown()
    {
        return $this->hasMany(UserFavouriteMenu::class, 'user_id');
    }
}
