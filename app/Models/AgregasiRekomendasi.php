<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgregasiRekomendasi extends Model
{
    protected $table = 'agregasi_rekomendasi';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'genre',
        'total_skor',
        'periode_mulai',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
