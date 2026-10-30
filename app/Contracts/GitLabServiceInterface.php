<?php

namespace App\Contracts;

/**
 * Interface for low-level interaction with the GitLab reports repository.
 *
 * Provides repository operations (tree listing, blob/file reading, and atomic commits)
 * without domain knowledge of report structures.
 */
interface GitLabServiceInterface {
  /**
   * Determine whether a folder or path exists (non-empty) in the repository.
   *
   * @param string $path
   * @return bool
   */
  public function pathExists(string $path): bool;

  /**
   * List tree entries under the given repository path.
   *
   * @param string $path
   * @param bool $recursive
   * @return array<int, array{id: string, name: string, path: string, type: 'blob'|'tree'}>
   */
  public function listTree(string $path, bool $recursive = false): array;

  /**
   * Read and decode JSON content of a repository blob by its SHA identifier.
   *
   * @param string $sha
   * @return array<string, mixed>|null
   */
  public function readBlob(string $sha): ?array;

  /**
   * Read and decode JSON content of a file located at the specified path.
   *
   * @param string $path
   * @return array<string, mixed>|null
   */
  public function readFile(string $path): ?array;

  /**
   * Perform an atomic multi-file commit in the repository.
   *
   * @param string $message Commit message.
   * @param array<int, array{action: 'create'|'update'|'delete'|'move', file_path: string, content?: string, previous_path?: string}> $actions List of commit actions.
   * @return void
   */
  public function commit(string $message, array $actions): void;
}
