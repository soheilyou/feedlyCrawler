<?php

namespace App\Providers;

use App\Repositories\Feed\FeedRepository;
use App\Repositories\Feed\FeedRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(FeedRepositoryInterface::class, FeedRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
