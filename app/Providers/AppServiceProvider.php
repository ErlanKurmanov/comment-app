<?php

namespace App\Providers;

use App\Models\News;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'news' => News::class,
            'video' => VideoPost::class,
        ]);

        Model::shouldBeStrict();
    }
}
