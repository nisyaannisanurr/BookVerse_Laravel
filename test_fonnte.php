<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$token = '43prdDXn41CGYThUXAYu';
$target = '082249219360';
$response = Illuminate\Support\Facades\Http::withHeaders([
    'Authorization' => $token,
])->post('https://api.fonnte.com/send', [
    'target' => $target,
    'message' => 'Test API',
    'countryCode' => '62'
]);

echo $response->body();
