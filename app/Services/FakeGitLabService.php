<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FakeGitLabService implements GitLabServiceInterface
{
  public function saveReport(User $user, string $filename, array $data): void
  {
    $path = "fake-gitlab/{$user->gitlab_path}/{$filename}";
    
    Storage::put($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    
//    Log::info('[FakeGitLabService] Skipped real GitLab commit', [
//      'user_id' => $user->id,
//      'gitlab_path' => $user->gitlab_path,
//      'filename' => $filename,
//    ]);
  }
  
  public function getReportsForWeek(User $user, \Carbon\Carbon $weekStart): array
  {
      return [
        'Montag' => ['taetigkeiten' => 'Fake-Bericht Montag'],
        'Dienstag' => ['taetigkeiten' => 'Fake-Bericht Dienstag'],
      ];
  }
}