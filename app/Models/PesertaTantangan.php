<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaTantangan extends Model
{
    protected $table = 'peserta_tantangans';
    protected $fillable = ['tantangan_id', 'user_id', 'buku_dibaca', 'status'];

    public function tantangan()
    {
        return $this->belongsTo(TantanganMembaca::class, 'tantangan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
