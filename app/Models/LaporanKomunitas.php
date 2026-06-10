<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanKomunitas extends Model
{
    protected $table = 'laporan_komunitas';

    protected $fillable = [
        'komunitas_id',
        'pelapor_id',
        'postingan_id',
        'komentar_id',
        'alasan',
        'status',
    ];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }

    public function postingan()
    {
        return $this->belongsTo(PostinganKomunitas::class, 'postingan_id');
    }

    public function komentar()
    {
        return $this->belongsTo(KomentarPostingan::class, 'komentar_id');
    }
}
