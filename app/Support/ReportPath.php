<?php

namespace App\Support;

use App\Models\User;
use Carbon\CarbonInterface;
use RuntimeException;

/**
 * Folder layout in the reports repository:
 *
 *   {gitlab_path}/2026/KW-40/2026-09-28.json   day report (Mon–Fri)
 *   {gitlab_path}/2026/KW-40/week.json         weekly report + status
 *
 * Year = ISO week year (KW 1 can start in December).
 */
final class ReportPath
{
  public const WEEK_FILE = 'Wochenbericht.json';

  public static function yearFolder(User $azubi, int $year): string
  {
    return self::root($azubi) . "/{$year}";
  }

  public static function weekFolder(User $azubi, int $year, int $week): string
  {
    return sprintf('%s/KW-%02d', self::yearFolder($azubi, $year), $week);
  }

  public static function weekFile(User $azubi, int $year, int $week): string
  {
    return self::weekFolder($azubi, $year, $week) . sprintf('/KW-%02d %s', $week, self::WEEK_FILE);
  }

  public static function dayFile(User $azubi, CarbonInterface $date): string
  {
    return self::weekFolder($azubi, $date->isoWeekYear(), $date->isoWeek()) . '/' . $date->format('Y-m-d') . '.json';
  }

  /** ".../2026/KW-40/2026-09-28.json" → ['path', 'year', 'week', 'kind' => 'day'|'week', 'date'] */
  public static function parse(string $path): ?array
  {
    if (! preg_match('#/(\d{4})/KW-(\d{2})/(KW-\d{2} Wochenbericht|\d{4}-\d{2}-\d{2})\.json$#', $path, $matches)) {
      return null;
    }

    return [
      'path' => $path,
      'year' => (int) $matches[1],
      'week' => (int) $matches[2],
      'kind' => str_ends_with($matches[3], 'Wochenbericht') ? 'week' : 'day',
      'date' => str_ends_with($matches[3], 'Wochenbericht') ? null : $matches[3],
    ];
  }

  private static function root(User $azubi): string
  {
    if (! $azubi->gitlab_path) {
      throw new RuntimeException("User #{$azubi->id} has no gitlab_path assigned yet.");
    }

    return $azubi->gitlab_path;
  }
}
