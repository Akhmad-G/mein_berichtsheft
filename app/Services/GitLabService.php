<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
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
}
