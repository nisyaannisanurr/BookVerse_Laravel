<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RakBukuKomunitas extends Model
{
    protected $table = 'rak_buku_komunitas';
    protected $fillable = ['komunitas_id', 'buku_id', 'tipe', 'catatan_admin'];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }
}
