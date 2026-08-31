<?php

namespace App\Contracts;

use App\Models\User;

interface GitLabServiceInterface
{
  public function saveReport(User $user, string $filename, array $data): void;
  
  public function getReportsForWeek(User $user, \Carbon\Carbon $weekStart): array;
}