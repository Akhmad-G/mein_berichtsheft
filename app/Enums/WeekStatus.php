<?php

namespace App\Enums;

enum WeekStatus: string
{
    case Draft     = 'draft';
    case Submitted = 'submitted';
    case Signed    = 'signed';

    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Offen',
            self::Submitted => 'Wartet',
            self::Signed    => 'Signiert',
        };
    }

    /** x-stamp status */
    public function stamp(): string
    {
        return match ($this) {
            self::Draft     => 'open',
            self::Submitted => 'pending',
            self::Signed    => 'signed',
        };
    }
}
