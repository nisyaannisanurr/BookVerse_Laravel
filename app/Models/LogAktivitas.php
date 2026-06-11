<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAktivitas extends Model
{
    protected $fillable = [
        'user_id', 'tipe_aksi', 'deskripsi', 'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($userId, $tipeAksi, $deskripsi)
    {
        return self::create([
            'user_id'    => $userId,
            'tipe_aksi'  => $tipeAksi,
            'deskripsi'  => $deskripsi,
            'ip_address' => request()->ip(),
        ]);
    }
}
