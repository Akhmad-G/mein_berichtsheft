<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use App\Support\GitLabPath;
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
  
  public function listReports(User $user): array
  {
    return [
      [
        'name' => '2026-08-31 Tagesbericht.json',
        'path' => "{$user->gitlab_path}/2026-08-31 Tagesbericht.json",
        'encoded_path' => GitLabPath::encode("{$user->gitlab_path}/2026-08-31 Tagesbericht.json"),
        'type' => 'tagesbericht',
      ],
      [
        'name' => 'KW35, 2026 Wochenbericht.json',
        'path' => "{$user->gitlab_path}/KW35, 2026 Wochenbericht.json",
        'encoded_path' => GitLabPath::encode("{$user->gitlab_path}/2026-KW35 Wochenbericht.json"),
        'type' => 'wochenbericht'],
    ];
  }
 
  public function getReport(User $user, string $path): array
  {
    return [
      'date' => '2026-08-31',
      'wochentag' => 'Montag',
      'taetigkeiten' => 'Fake-Inhalt für ' . $path,
    ];
  }
}