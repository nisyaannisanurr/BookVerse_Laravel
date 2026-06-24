<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TantanganMembaca extends Model
{
    protected $table = 'tantangan_membacas';
    protected $fillable = ['komunitas_id', 'judul', 'deskripsi', 'target_buku', 'tanggal_mulai', 'tanggal_selesai'];
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function peserta()
    {
        return $this->hasMany(PesertaTantangan::class, 'tantangan_id');
    }

    public function postingan()
    {
        return $this->hasMany(PostinganKomunitas::class, 'tantangan_id');
    }
}
