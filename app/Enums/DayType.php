<?php

namespace App\Enums;

enum DayType: string
{
    case Work     = 'work';
    case School   = 'school';
    case Vacation = 'vacation';
    case Sick     = 'sick';
    case Holiday  = 'holiday';

    /** UI label (German). */
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

    public function isWork(): bool
    {
        return $this === self::Work;
    }

    /** Calendar cell state: report|open|school|vacation|sick|holiday */
    public function calendarState(bool $hasContent): string
    {
        return $this->isWork() ? ($hasContent ? 'report' : 'open') : $this->value;
    }

    /** x-stamp status */
    public function stamp(bool $hasContent): string
    {
        return $this->isWork() ? ($hasContent ? 'recorded' : 'open') : $this->value;
    }
}
