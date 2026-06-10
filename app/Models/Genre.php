<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $table = 'genres';

    protected $fillable = ['nama_genre', 'deskripsi', 'warna'];

    /** Total buku per genre */
    public function bukus()
    {
        return $this->hasMany(Buku::class, 'genre_buku', 'nama_genre');
    }

    public static function getAllOrdered()
    {
        return self::orderBy('nama_genre')->get();
    }
}
