<?php

namespace App\Contracts;

/**
 * Low-level access to the reports repository.
 * Knows nothing about days/weeks — folder layout lives in App\Support\ReportPath,
 * domain logic in App\Repositories\ReportRepository.
 */
interface GitLabServiceInterface {
  /** Does a (non-empty) folder exist? */
  public function pathExists(string $path): bool;

  /** Tree entries under $path: [['id' => sha, 'name', 'path', 'type' => 'blob'|'tree'], …]. Missing path → []. */
  public function listTree(string $path, bool $recursive = false): array;

  /** Decoded JSON of a blob by sha. Blobs are immutable → cached forever. */
  public function readBlob(string $sha): ?array;

  /** Decoded JSON of a file by path, null if it does not exist. */
  public function readFile(string $path): ?array;

  /**
   * One commit with several actions.
   * Action: ['action' => 'create'|'update'|'delete'|'move', 'file_path' => …, 'content' => …, 'previous_path' => …]
   */
  public function commit(string $message, array $actions): void;
}
