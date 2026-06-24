<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InstansiDaerah;

class InstansiDaerahSeeder extends Seeder
{
    public function run(): void
    {
        $daerahs = [
            'Bantan Air', 'Bantan Sari', 'Bantan Tengah', 'Bantan Timur', 
            'Bantan Tua', 'Berancah', 'Deluk', 'Jangkang', 'Kembung Baru', 
            'Kembung Luar', 'Mentayan', 'Muntai', 'Muntai Barat', 
            'Pampang Baru', 'Pampang Pesisir', 'Pasiran', 'Resam Lapis', 
            'Selat Baru', 'Sukamaju', 'Teluklancar', 'Telukpambang', 
            'Telukpapal', 'Ulu Pulau'
        ];

        foreach ($daerahs as $daerah) {
            InstansiDaerah::firstOrCreate([
                'nama_daerah' => $daerah
            ]);
        }
    }
}
