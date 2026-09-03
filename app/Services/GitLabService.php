<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use App\Support\GitLabPath;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use function Pest\Laravel\withHeaders;

class GitLabService implements GitLabServiceInterface
{
    protected string $baseUrl;
    protected string $token;
    protected string $projectId;
    protected string $branch = 'main';
    
    public function __construct()
    {
        $this->baseUrl = config('services.gitlab.url');
        $this->token = config('services.gitlab.token');
        $this->projectId = config('services.gitlab.project_id');
    }
  
    /**
     * Saves a report (Tagesbericht or Wochenbericht) as a commit in GitLab.
     *
     * @param User $user
     * @param string $filename e.g., "2026-08-25 Tagesbericht.json"
     * @param array $data report data, to be encoded as JSON
     */
    
    public function saveReport(User $user, string $filename, array $data): void
    {
//        dd([
//          'url' => "{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/commits",
//          'project_id' => $this->projectId,
//          'branch' => $this->branch,
//        ]);
        
        if (! $user->gitlab_path) {
          throw new \RuntimeException("User #{$user->id} has no gitlab_path assigned yet.");
        }
        
        $filePath = "{$user->gitlab_path}/{$filename}";
        
        $content = json_encode($data,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        $response = Http::withHeaders([
            'PRIVATE-TOKEN' => $this->token,
        ])->post("{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/commits", [
            'branch' => $this->branch,
            'commit_message' => "Add {$filename} for {$user->gitlab_path}",
            'actions' => [
                [
                    'action' => 'create',
                    'file_path' => $filePath,
                    'content' => $content,
                ],
            ],
        ]);
        
        if ($response->failed()) {
            Log::error('GitLab commit failed', [
                'user_id' => $user->id,
                'file_path' => $filePath,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
          
          throw new \RuntimeException(
              "GitLab commit failed for {$filePath}: {$response->status()} {$response->body()}"
          );
        }
    }
  
    public function getReportsForWeek(User $user, \Carbon\Carbon $weekStart): array
    {
        $weekEnd = $weekStart->copy()->endOfWeek();
        
        // 1. Get the list of files in the user's folder.
        $response = Http::withHeaders([
          'PRIVATE-TOKEN' => $this->token,
        ])->get("{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/tree", [
          'path' => $user->gitlab_path,
          'ref' => $this->branch,
          'per_page' => 100,
        ]);
      
        if ($response->failed()) {
          throw new \RuntimeException("Failed to list files for {$user->gitlab_path}: {$response->status()}");
        }
      
        $files = collect($response->json())
          ->filter(fn ($file) => str_ends_with($file['name'], 'Tagesbericht.json'))
          ->filter(function ($file) use ($weekStart, $weekEnd) {
            // filename in the format "2026-08-31 Tagesbericht.json"
            $date = \Carbon\Carbon::parse(substr($file['name'], 0, 10));
            return $date->between($weekStart, $weekEnd);
          });
      
      // 2. Download the contents of each file.
      $reportsByWeekday = [];
      
      foreach ($files as $file) {
        $encodedPath = rawurlencode($file['path']);
        
        $fileResponse = Http::withHeaders([
          'PRIVATE-TOKEN' => $this->token,
        ])->get("{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/files/{$encodedPath}/raw", [
          'ref' => $this->branch,
        ]);
        
        if ($fileResponse->failed()) {
          Log::warning('Failed to fetch report', ['path' => $file['path']]);
          continue;
        }
        
        $data = json_decode($fileResponse->body(), true);
        $weekday = \Carbon\Carbon::parse($data['date'])->translatedFormat('l'); // Montag, Dienstag, ...
        
        $reportsByWeekday[$weekday] = $data;
      }
      
      return $reportsByWeekday;
    }
  
    public function listReports(User $user): array
    {
      if (! $user->gitlab_path) {
        return [];
      }
      
      $response = Http::withHeaders([
        'PRIVATE-TOKEN' => $this->token,
      ])->get("{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/tree", [
        'path' => $user->gitlab_path,
        'ref' => $this->branch,
        'per_page' => 100,
      ]);
      
      if ($response->failed()) {
        Log::error('Failed to list reports', [
          'user_id' => $user->id,
          'status' => $response->status(),
        ]);
        return [];
      }
      
      return collect($response->json())
        ->filter(fn ($file) => str_ends_with($file['name'], '.json'))
        ->map(function ($file) {
          $type = str_contains($file['name'], 'Wochenbericht') ? 'wochenbericht' : 'tagesbericht';
          
          return [
            'name' => $file['name'],
            'path' => $file['path'],
            'encoded_path' => GitLabPath::encode($file['path']),
            'type' => $type,
          ];
        })
        ->sortByDesc('name') // Die Namen beginnen mit dem Datum, daher gilt: Sortieren nach Name = Sortieren nach Datum
        ->values()
        ->all();
    }
  
    public function getReport(User $user, string $path): array
    {
      $encodedPath = rawurlencode($path);
      
      $response = Http::withHeaders([
        'PRIVATE-TOKEN' => $this->token,
      ])->get("{$this->baseUrl}/api/v4/projects/{$this->projectId}/repository/files/{$encodedPath}/raw", [
        'ref' => $this->branch,
      ]);
      
      if ($response->failed()) {
        throw new \RuntimeException("Failed to fetch report {$path}: {$response->status()}");
      }
      
      return json_decode($response->body(), true);
    }
}
