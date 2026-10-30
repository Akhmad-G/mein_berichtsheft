<?php

namespace App\Data;

use App\Enums\DayType;
use Carbon\CarbonImmutable;

/**
 * Data Transfer Object representing a daily training report.
 * Stored as JSON: {year}/KW-{week}/{date}.json
 */
final class DayReport
{
    /**
     * @param CarbonImmutable $date Date of the report entry.
     * @param DayType $type Type of the day (work, school, vacation, sick, holiday).
     * @param string|null $activities Description of executed work / training activities.
     * @param string|null $duration Duration or hours spent on activities.
     * @param string|null $department Department where work took place.
     * @param array<int, string> $learningSteps List of learning objectives / steps covered.
     * @param string|null $note Additional notes or comments.
     * @param bool $exists Whether the report file exists in the repository.
     */
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

    /**
     * Create a DayReport instance from parsed JSON storage data.
     *
     * @param array<string, mixed> $data Decoded JSON data from the storage file.
     * @param CarbonImmutable $date The date corresponding to this report.
     * @return self
     */
    public static function fromArray(array $data, CarbonImmutable $date): self
    {
        return new self(
            date: $date,
            type: DayType::fromStorageValue($data['typ'] ?? ''),
            activities: $data['taetigkeiten'] ?? null,
            duration: $data['dauer'] ?? null,
            department: $data['abteilung'] ?? null,
            learningSteps: $data['lernziele'] ?? [],
            note: $data['notiz'] ?? null,
            exists: true,
        );
    }

    /**
     * Convert the DayReport instance into an array suitable for JSON storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'datum'         => $this->date->toDateString(),
            'typ'           => $this->type->storageValue(),
            'taetigkeiten'  => $this->activities,
            'dauer'         => $this->duration,
            'abteilung'     => $this->department,
            'lernziele'     => $this->learningSteps,
            'notiz'         => $this->note,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);
    }

    /**
     * Check if the report has non-empty activity content.
     *
     * @return bool
     */
    public function hasContent(): bool
    {
        return filled($this->activities);
    }

    /**
     * Determine if this is an empty work day that does not require repository storage.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->type->isWork()
            && blank($this->activities)
            && blank($this->note)
            && $this->learningSteps === [];
    }

    /**
     * Get the calendar cell state identifier for UI rendering.
     *
     * @return string
     */
    public function calendarState(): string
    {
        return $this->type->calendarState($this->hasContent());
    }

    /**
     * Get the stamp indicator status string.
     *
     * @return string
     */
    public function stamp(): string
    {
        return $this->type->stamp($this->hasContent());
    }

    /**
     * Get the human-readable status label in German.
     *
     * @return string
     */
    public function statusLabel(): string
    {
        return $this->type->isWork()
            ? ($this->hasContent() ? 'Erfasst' : 'Offen')
            : $this->type->label();
    }
}
