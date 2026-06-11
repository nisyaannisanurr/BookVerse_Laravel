<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanGlobal extends Model
{
    protected $fillable = [
        'pelapor_id', 'tipe_entitas', 'entitas_id', 'alasan', 
        'detail_tambahan', 'status', 'catatan_admin'
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id');
    }
}
