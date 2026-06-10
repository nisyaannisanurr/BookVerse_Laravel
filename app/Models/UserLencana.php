<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLencana extends Model
{
    protected $table = 'user_lencanas';
    protected $fillable = ['user_id', 'lencana_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lencana()
    {
        return $this->belongsTo(LencanaKomunitas::class, 'lencana_id');
    }
}
