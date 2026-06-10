<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostinganLike extends Model
{
    protected $table = 'postingan_likes';
    protected $fillable = ['postingan_id', 'user_id'];

    public function postingan()
    {
        return $this->belongsTo(PostinganKomunitas::class, 'postingan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
