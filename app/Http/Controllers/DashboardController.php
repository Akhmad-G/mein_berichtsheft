<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index(GitLabServiceInterface $gitLabService)
  {
      $user = auth()->user();
      
      if ($user->isAusbilder()) {
          $reports = $user->azubis
              ->flatMap(function ($azubi) use ($gitLabService) {
                  return collect($gitLabService->listReports($azubi))
                      ->map(function (array $report) use ($azubi) {
                          $report['azubi_name'] = $azubi->name;
                          
                          return $report;
                      });
              })
              ->sortByDesc('name')
              ->values()
              ->all();
      } else {
          $reports = $gitLabService->listReports($user);
      }
      
      return view('dashboard', ['reports' => $reports]);
  }
}
