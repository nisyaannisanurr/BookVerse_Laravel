<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LencanaKomunitas extends Model
{
    protected $table = 'lencana_komunitas';
    protected $fillable = ['komunitas_id', 'nama_lencana', 'ikon', 'deskripsi'];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_lencanas', 'lencana_id', 'user_id')->withTimestamps();
    }
}
