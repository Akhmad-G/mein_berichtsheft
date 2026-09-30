<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GitLabService implements GitLabServiceInterface {
  protected string $baseUrl;
  protected string $token;
  protected string $projectId;
  protected string $branch;

  public function __construct() {
    $this->baseUrl   = rtrim(config('services.gitlab.url'), '/');
    $this->token     = config('services.gitlab.token');
    $this->projectId = config('services.gitlab.project_id');
    $this->branch    = config('services.gitlab.branch', 'main');
  }

  public function pathExists(string $path): bool {
    $response = $this->api()->get('tree', [
      'path' => $path,
      'ref' => $this->branch,
      'per_page' => 1
    ]);

    return $response->successful() && $response->json() !== [];
  }

  public function listTree(string $path, bool $recursive = false): array
  {
    $items = [];
    $page  = 1;

    do {
      $response = $this->api()->get('tree', [
        'path'      => $path,
        'ref'       => $this->branch,
        'recursive' => $recursive ? 'true' : 'false',
        'per_page'  => 100,
        'page'      => $page,
      ]);

      if ($response->status() === 404) {
        return [];
      }
      if ($response->failed()) {
        throw new RuntimeException("Failed to list {$path}: {$response->status()}");
      }

      array_push($items, ...$response->json());
      $page = (int) $response->header('X-Next-Page');
    } while ($page > 0);

    return $items;
  }

  public function readBlob(string $sha): ?array
  {
    return Cache::rememberForever("gitlab:blob:{$sha}", function () use ($sha) {
      $response = $this->api()->get("blobs/{$sha}/raw");

      if ($response->failed()) {
        throw new RuntimeException("Failed to fetch blob {$sha}: {$response->status()}");
      }

      return json_decode($response->body(), true);
    });
  }

  public function readFile(string $path): ?array
  {
    $response = $this->api()->get('files/' . rawurlencode($path) . '/raw', ['ref' => $this->branch]);

    if ($response->status() === 404) {
      return null;
    }
    if ($response->failed()) {
      throw new RuntimeException("Failed to fetch {$path}: {$response->status()}");
    }

    return json_decode($response->body(), true);
  }

  public function commit(string $message, array $actions): void
  {
    if ($actions === []) {
      return;
    }

    $response = $this->api()->post('commits', [
      'branch'         => $this->branch,
      'commit_message' => $message,
      'actions'        => $actions,
    ]);

    if ($response->failed()) {
      Log::error('GitLab commit failed', [
        'message' => $message,
        'paths'   => array_column($actions, 'file_path'),
        'status'  => $response->status(),
        'body'    => $response->body(),
      ]);

      throw new RuntimeException("GitLab commit failed: {$response->status()} {$response->body()}");
    }
  }

  protected function api(): PendingRequest
  {
    return Http::withHeaders(['PRIVATE-TOKEN' => $this->token])
      ->baseUrl("{$this->baseUrl}/api/v4/projects/" . rawurlencode($this->projectId) . '/repository')
      ->acceptJson()
      ->timeout(15);
  }
}
