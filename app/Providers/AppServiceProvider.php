<?php

namespace App\Providers;

use App\Services\FakeGitLabService;
use App\Contracts\GitLabServiceInterface;
use App\Services\GitLabService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
      $this->app->bind(GitLabServiceInterface::class, function () {
        return config('services.gitlab.fake', false)
          ? app(FakeGitLabService::class)
          : app(GitLabService::class);
      });
      
//        $this->app->singleton(GitLabService::class, function ($app) {
//          return new GitLabService();
//        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
