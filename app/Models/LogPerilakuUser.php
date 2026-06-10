<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPerilakuUser extends Model
{
    protected $table = 'log_perilaku_user';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tipe_aktivitas',
        'bobot',
        'metadata',
        'genre_terkait',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
