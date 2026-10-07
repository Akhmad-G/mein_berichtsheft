<?php

namespace App\Data;

use App\Enums\DayType;
use Carbon\CarbonImmutable;

/** One day file: {year}/KW-NN/YYYY-MM-DD.json */
final class DayReport
{
    public function __construct(
        public readonly CarbonImmutable $date,
        public DayType $type = DayType::Work,
        public ?string $activities = null,
        public ?string $duration = null,
        public ?string $department = null,
        public array $learningSteps = [],
        public ?string $note = null,
        public bool $exists = false,
    ) {}

    /** JSON keys → properties. If your existing files use other keys, map them here. */
    public static function fromArray(array $data, CarbonImmutable $date): self
    {
        return new self(
            date: $date,
            type: DayType::tryFrom($data['typ'] ?? '') ?? DayType::Work,
            activities: $data['taetigkeiten'] ?? null,
            duration: $data['dauer'] ?? null,
            department: $data['abteilung'] ?? null,
            learningSteps: $data['lernziele'] ?? [],
            note: $data['notiz'] ?? null,
            exists: true,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'datum'         => $this->date->toDateString(),
            'typ'           => $this->type->value,
            'taetigkeiten'  => $this->activities,
            'dauer'         => $this->duration,
            'abteilung'     => $this->department,
            'lernziele'     => $this->learningSteps,
            'notiz'         => $this->note,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    public function hasContent(): bool
    {
        return filled($this->activities);
    }

    /** Empty work day → no file in the repository. */
    public function isEmpty(): bool
    {
        return $this->type->isWork()
            && blank($this->activities)
            && blank($this->note)
            && $this->learningSteps === [];
    }

    public function calendarState(): string
    {
        return $this->type->calendarState($this->hasContent());
    }

    public function stamp(): string
    {
        return $this->type->stamp($this->hasContent());
    }

    public function statusLabel(): string
    {
        return $this->type->isWork()
            ? ($this->hasContent() ? 'Erfasst' : 'Offen')
            : $this->type->label();
    }
}
