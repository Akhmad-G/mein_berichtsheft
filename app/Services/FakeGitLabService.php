<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FakeGitLabService implements GitLabServiceInterface
{
  public function saveReport(User $user, string $filename, array $data): void
  {
    Log::info('[FakeGitLabService] Skipped real GitLab commit', [
      'user_id' => $user->id,
      'gitlab_path' => $user->gitlab_path,
      'filename' => $filename,
    ]);
  }
  
  public function getReportsForWeek(User $user, \Carbon\Carbon $weekStart): array
  {
      return [
        'Montag' => ['taetigkeiten' => 'Fake-Bericht Montag'],
        'Dienstag' => ['taetigkeiten' => 'Fake-Bericht Dienstag'],
      ];
  }
}