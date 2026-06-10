<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PertanyaanQna extends Model
{
    protected $table = 'pertanyaan_qnas';
    protected $fillable = ['qna_id', 'user_id', 'pertanyaan', 'jawaban', 'is_highlighted'];

    public function qna()
    {
        return $this->belongsTo(QnaKomunitas::class, 'qna_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
