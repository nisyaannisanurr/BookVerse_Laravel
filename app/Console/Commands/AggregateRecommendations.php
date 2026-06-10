<?php

namespace App\Console\Commands;

use App\Models\LogPerilakuUser;
use App\Services\RecommendationService;
use Illuminate\Console\Command;

class AggregateRecommendations extends Command
{
    protected $signature   = 'rec:aggregate {--user= : User ID specific}';
    protected $description = 'Aggregate recommendation data for all users or a specific user';

    public function handle(RecommendationService $svc): void
    {
        $userId = $this->option('user');

        if ($userId) {
            $svc->aggregateForUser((int) $userId);
            $this->info("Aggregated for user $userId");
            return;
        }

        $users = LogPerilakuUser::whereNotNull('genre_terkait')
            ->distinct()
            ->pluck('user_id');

        foreach ($users as $uid) {
            $svc->aggregateForUser($uid);
            $this->line("  Aggregated user $uid");
        }

        $this->info("Done. Total users: " . $users->count());
    }
}
