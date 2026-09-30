<?php

namespace App\Providers;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use App\Repositories\ReportRepository;
use App\Services\GitLabService;
use App\View\Composers\TabsComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
  /**
   * Register any application services.
   */
  public function register(): void {
    $this->app->bind(GitLabServiceInterface::class, GitLabService::class);
    $this->app->scoped(ReportRepository::class); // one per request — tree listing is memoized
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void {
    View::composer(['calendar.index', 'weekly-reports.index', 'azubis.index'], TabsComposer::class);

    // own reports, or the Ausbilder of this Azubi
    Gate::define('view-reports', fn (User $viewer, User $azubi) =>
      $viewer->is($azubi) || ($viewer->isAusbilder() && $azubi->ausbilder_id === $viewer->id));

    // only the Azubi writes and submits
    Gate::define('edit-reports', fn (User $viewer, User $azubi) =>
      $viewer->is($azubi) && $viewer->isAzubi());

    // only the assigned Ausbilder signs
    Gate::define('sign-reports', fn (User $viewer, User $azubi) =>
      $viewer->isAusbilder() && $azubi->ausbilder_id === $viewer->id);
  }
}
