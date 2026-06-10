<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RakBukuUser extends Model
{
    protected $table = 'rak_buku_user';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'buku_id',
        'status',
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
