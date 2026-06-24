<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstansiDaerah extends Model
{
    protected $table = 'instansi_daerahs';

    protected $fillable = [
        'nama_daerah',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function mitraVerifications()
    {
        return $this->hasMany(MitraVerification::class, 'instansi_daerah_id');
    }
}
