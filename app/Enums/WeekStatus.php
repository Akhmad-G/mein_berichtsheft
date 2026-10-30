<?php

namespace App\Enums;

/**
 * Represents the workflow status of a weekly report.
 */
enum WeekStatus: string
{
    case Draft     = 'draft';
    case Submitted = 'submitted';
    case Signed    = 'signed';

    /**
     * Get the human-readable UI label in German.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Offen',
            self::Submitted => 'Wartet',
            self::Signed    => 'Signiert',
        };
    }

    /**
     * Get the status code for the x-stamp UI component.
     *
     * @return string 'open'|'pending'|'signed'
     */
    public function stamp(): string
    {
        return match ($this) {
            self::Draft     => 'open',
            self::Submitted => 'pending',
            self::Signed    => 'signed',
        };
    }
}
