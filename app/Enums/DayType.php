<?php

namespace App\Enums;

/**
 * Represents the type of a daily training report entry.
 */
enum DayType: string
{
    case Work     = 'work';
    case School   = 'school';
    case Vacation = 'vacation';
    case Sick     = 'sick';
    case Holiday  = 'holiday';

    /**
     * Get the human-readable UI label in German.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Work     => 'Arbeitstag',
            self::School   => 'Berufsschule',
            self::Vacation => 'Urlaub',
            self::Sick     => 'Krank',
            self::Holiday  => 'Feiertag',
        };
    }

    /**
     * Get the storage key value used in report JSON files.
     *
     * @return string
     */
    public function storageValue(): string
    {
      return match ($this) {
        self::Work => 'arbeitstag',
        self::School => 'schule',
        self::Vacation => 'urlaub',
        self::Sick => 'krank',
        self::Holiday => 'feiertag',
      };
    }

    /**
     * Create an instance from a storage string value.
     *
     * @param string $value
     * @return self
     */
    public static function fromStorageValue(string $value): self
    {
      return match ($value) {
        'arbeitstag', 'work' => self::Work,
        'schule', 'school' => self::School,
        'urlaub', 'vacation' => self::Vacation,
        'krank', 'sick' => self::Sick,
        'feiertag', 'holiday' => self::Holiday,
        default => self::Work,
      };
    }

    /**
     * Check if the day is a regular work day.
     *
     * @return bool
     */
    public function isWork(): bool
    {
        return $this === self::Work;
    }

    /**
     * Determine the calendar cell state identifier.
     *
     * @param bool $hasContent Whether the report has recorded activity content.
     * @return string 'report'|'open'|'school'|'vacation'|'sick'|'holiday'
     */
    public function calendarState(bool $hasContent): string
    {
        return $this->isWork() ? ($hasContent ? 'report' : 'open') : $this->value;
    }

    /**
     * Determine the stamp component status for display.
     *
     * @param bool $hasContent Whether the report has recorded activity content.
     * @return string 'recorded'|'open'|'school'|'vacation'|'sick'|'holiday'
     */
    public function stamp(bool $hasContent): string
    {
        return $this->isWork() ? ($hasContent ? 'recorded' : 'open') : $this->value;
    }
}
