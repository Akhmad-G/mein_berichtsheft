<?php

namespace App\Contracts;

use App\Models\User;

interface GitLabServiceInterface
{
  public function saveReport(User $user, string $filename, array $data): void;
}