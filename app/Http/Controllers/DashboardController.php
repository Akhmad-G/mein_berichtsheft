<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
  public function index(GitLabServiceInterface $gitLabService)
  {
    $reports = $gitLabService->listReports(auth()->user());
    
    return view('dashboard', ['reports' => $reports]);
  }
}
