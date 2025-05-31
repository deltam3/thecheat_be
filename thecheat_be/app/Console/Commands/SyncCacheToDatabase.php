<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\Post;
use App\Models\UserPoint;

class SyncCacheToDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-cache-to-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Redis cache data (view counts and user points) to MySQL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::all();

        foreach ($posts as $post) {
            $viewCountCacheKey = "post:{$post->id}:view_count";
            $cachedViewCount = Redis::get($viewCountCacheKey);

            if ($cachedViewCount && $cachedViewCount != $post->view_count) {
                $post->update(['view_count' => $cachedViewCount]);
            }

            $users = UserPoint::where('activity_reference_id', $post->id)
                ->where('activity_type', 'read')
                ->distinct()
                ->get();

            foreach ($users as $user) {
                $userPointsCacheKey = "user:{$user->id}:points";
                $cachedPoints = Redis::get($userPointsCacheKey);
                
                if ($cachedPoints) {
                    UserPoint::updateOrCreate(
                        ['user_id' => $user->id, 'activity_reference_id' => $post->id],
                        ['points' => $cachedPoints]
                    );
                }
            }
        }

        $this->info('Cache synchronized with the database.');
    }
}
