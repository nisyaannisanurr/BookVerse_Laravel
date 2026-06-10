<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

$svc = $app->make(App\Services\RecommendationService::class);

// Re-aggregate for all users that have logs
$userIds = Illuminate\Support\Facades\DB::table('log_perilaku_user')
    ->distinct()->pluck('user_id');

foreach ($userIds as $uid) {
    $svc->aggregateForUser($uid);
    echo "Aggregated user: $uid\n";
}

$results = Illuminate\Support\Facades\DB::table('agregasi_rekomendasi')
    ->orderBy('user_id')->orderByDesc('total_skor')->get();

echo "\n=== AGREGASI TERBARU ===\n";
foreach ($results as $r) {
    echo "User {$r->user_id} | {$r->genre} | skor: {$r->total_skor}\n";
}
