<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QnaKomunitas extends Model
{
    protected $table = 'qna_komunitas';
    protected $fillable = ['komunitas_id', 'judul', 'narasumber', 'status'];

    public function komunitas()
    {
        return $this->belongsTo(Komunitas::class, 'komunitas_id');
    }

    public function pertanyaan()
    {
        return $this->hasMany(PertanyaanQna::class, 'qna_id');
    }
}
