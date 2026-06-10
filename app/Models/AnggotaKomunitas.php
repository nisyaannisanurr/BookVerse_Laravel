<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggotaKomunitas extends Model
{
    protected $table = 'anggota_komunitas';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'komunitas_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }
}
