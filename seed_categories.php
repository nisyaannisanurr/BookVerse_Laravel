<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cats = \App\Models\Panduan::select('kategori')->distinct()->pluck('kategori');
foreach($cats as $cat) {
    \App\Models\KategoriPanduan::firstOrCreate(['nama' => $cat]);
}
echo "Done seeding categories.\n";
