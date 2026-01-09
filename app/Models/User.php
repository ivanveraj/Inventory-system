<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable implements FilamentUser
{
    use Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'username',
        'phone_number',
        'status',
        'password',
        'email_verified_at',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /*   public function Rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    } */

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
