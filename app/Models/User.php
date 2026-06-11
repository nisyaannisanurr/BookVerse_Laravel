<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'foto_profil',
        'bio',
        'google_id',
        'avatar',
        'status_akun',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role_id' => 'integer',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function lencana()
    {
        return $this->belongsToMany(LencanaKomunitas::class, 'user_lencanas', 'user_id', 'lencana_id')->withTimestamps();
    }

    public function tantangan()
    {
        return $this->hasMany(PesertaTantangan::class, 'user_id');
    }

    public function pertanyaanQna()
    {
        return $this->hasMany(PertanyaanQna::class, 'user_id');
    }

    public function rakBuku()
    {
        return $this->hasMany(RakBukuUser::class, 'user_id');
    }

    public function ratings()
    {
        return $this->hasMany(RatingBuku::class, 'user_id');
    }

    public function komunitasCreated()
    {
        return $this->hasMany(Komunitas::class, 'creator_id');
    }

    public function anggotaKomunitas()
    {
        return $this->hasMany(AnggotaKomunitas::class, 'user_id');
    }

    public function prelovedBooks()
    {
        return $this->hasMany(PrelovedBook::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }

    // Helper methods
    public function isSuperadmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isAdminKomunitas(): bool
    {
        return $this->role_id === 2;
    }

    public function isUser(): bool
    {
        return $this->role_id === 3;
    }

    // Scopes
    public function scopeNonSuperadmin($query)
    {
        return $query->where('role_id', '!=', 1);
    }
}
