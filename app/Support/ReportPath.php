<?php

namespace App\Support;

use App\Models\User;
use Carbon\CarbonInterface;
use RuntimeException;

/**
 * Utility class for resolving and parsing file and directory paths in the reports repository.
 *
 * Folder layout:
 *   {gitlab_path}/{year}/KW-{week}/{date}.json              (Day report, Mon–Fri)
 *   {gitlab_path}/{year}/KW-{week}/KW-{week} Wochenbericht.json (Weekly report summary)
 */
final class ReportPath
{
  /**
   * Filename suffix for weekly report summary JSON files.
   */
  public const WEEK_FILE = 'Wochenbericht.json';

  /**
   * Get the repository folder path for an apprentice's year.
   *
   * @param User $azubi
   * @param int $year
   * @return string
   */
  public static function yearFolder(User $azubi, int $year): string
  {
    return self::root($azubi) . "/{$year}";
  }

  /**
   * Get the repository folder path for an apprentice's specific calendar week.
   *
   * @param User $azubi
   * @param int $year
   * @param int $week
   * @return string
   */
  public static function weekFolder(User $azubi, int $year, int $week): string
  {
    return sprintf('%s/KW-%02d', self::yearFolder($azubi, $year), $week);
  }

  /**
   * Get the weekly report JSON file path.
   *
   * @param User $azubi
   * @param int $year
   * @param int $week
   * @return string
   */
  public static function weekFile(User $azubi, int $year, int $week): string
  {
    return self::weekFolder($azubi, $year, $week) . sprintf('/KW-%02d %s', $week, self::WEEK_FILE);
  }

  /**
   * Get the daily report JSON file path for a given date.
   *
   * @param User $azubi
   * @param CarbonInterface $date
   * @return string
   */
  public static function dayFile(User $azubi, CarbonInterface $date): string
  {
    return self::weekFolder($azubi, $date->isoWeekYear(), $date->isoWeek()) . '/' . $date->format('Y-m-d') . '.json';
  }

  /**
   * Parse a repository path into metadata components (year, week, kind, date).
   *
   * @param string $path
   * @return array{path: string, year: int, week: int, kind: 'day'|'week', date: string|null}|null
   */
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

  /**
   * Get the root GitLab folder path for an apprentice.
   *
   * @param User $azubi
   * @return string
   *
   * @throws RuntimeException If no gitlab_path has been assigned to the user.
   */
  private static function root(User $azubi): string
  {
    if (! $azubi->gitlab_path) {
      throw new RuntimeException("User #{$azubi->id} has no gitlab_path assigned yet.");
    }

    return $azubi->gitlab_path;
  }
}
