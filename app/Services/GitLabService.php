<?php

namespace App\Services;

use App\Contracts\GitLabServiceInterface;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Service implementing low-level HTTP interaction with the GitLab REST API repository endpoints.
 */
class GitLabService implements GitLabServiceInterface {
  /**
   * Base URL of the GitLab instance.
   *
   * @var string
   */
  protected string $baseUrl;

  /**
   * GitLab API private access token.
   *
   * @var string
   */
  protected string $token;

  /**
   * GitLab project ID or URL-encoded path with namespace.
   *
   * @var string
   */
  protected string $projectId;

  /**
   * Target repository branch name.
   *
   * @var string
   */
  protected string $branch;

  /**
   * Initialize GitLab service configuration from environment settings.
   */
  public function __construct() {
    $this->baseUrl   = rtrim(config('services.gitlab.url'), '/');
    $this->token     = config('services.gitlab.token');
    $this->projectId = config('services.gitlab.project_id');
    $this->branch    = config('services.gitlab.branch', 'main');
  }

  /**
   * Determine whether a folder or path exists (non-empty) in the repository.
   *
   * @param string $path
   * @return bool
   */
  public function pathExists(string $path): bool {
    $response = $this->api()->get('tree', [
      'path' => $path,
      'ref' => $this->branch,
      'per_page' => 1
    ]);

    return $response->successful() && $response->json() !== [];
  }

  /**
   * List tree entries under the given repository path with optional pagination.
   *
   * @param string $path
   * @param bool $recursive
   * @return array<int, array{id: string, name: string, path: string, type: 'blob'|'tree'}>
   *
   * @throws RuntimeException If GitLab API returns a failure status other than 404.
   */
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

  /**
   * Read and decode JSON content of a repository blob by its SHA, cached indefinitely.
   *
   * @param string $sha
   * @return array<string, mixed>|null
   *
   * @throws RuntimeException If fetching blob fails.
   */
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

  /**
   * Read and decode JSON content of a file located at the specified repository path.
   *
   * @param string $path
   * @return array<string, mixed>|null
   *
   * @throws RuntimeException If fetching file fails with an error status other than 404.
   */
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

  /**
   * Commit a batch of file actions (create, update, delete, move) atomically.
   *
   * @param string $message Commit message.
   * @param array<int, array{action: 'create'|'update'|'delete'|'move', file_path: string, content?: string, previous_path?: string}> $actions
   * @return void
   *
   * @throws RuntimeException If GitLab commit request fails.
   */
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

  /**
   * Build a pre-configured HTTP client request targeting GitLab repository API.
   *
   * @return PendingRequest
   */
  protected function api(): PendingRequest
  {
    return Http::withHeaders(['PRIVATE-TOKEN' => $this->token])
      ->baseUrl("{$this->baseUrl}/api/v4/projects/" . rawurlencode($this->projectId) . '/repository')
      ->acceptJson()
      ->timeout(15);
  }
}
