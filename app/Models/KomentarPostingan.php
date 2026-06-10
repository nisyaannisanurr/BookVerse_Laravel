<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomentarPostingan extends Model
{
    protected $table = 'komentar_postingan';
    const UPDATED_AT = null; // tabel tidak punya kolom updated_at

    protected $fillable = [
        'postingan_id',
        'user_id',
        'konten',
        'gambar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function postingan()
    {
        return $this->belongsTo(PostinganKomunitas::class, 'postingan_id');
    }
}
