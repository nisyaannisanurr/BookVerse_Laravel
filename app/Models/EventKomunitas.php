<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventKomunitas extends Model
{
    protected $table = 'event_komunitas';

    protected $fillable = [
        'komunitas_id',
        'judul',
        'deskripsi',
        'tanggal_waktu',
        'lokasi',
    ];

    protected $casts = [
        'tanggal_waktu' => 'datetime',
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }
}
