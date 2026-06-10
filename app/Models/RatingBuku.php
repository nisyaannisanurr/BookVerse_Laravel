<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RatingBuku extends Model
{
    protected $table = 'rating_buku';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'buku_id',
        'skor_rating',
        'ulasan_teks',
    ];

    protected $casts = [
        'skor_rating' => 'integer',
        'tanggal_rating' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }
}
