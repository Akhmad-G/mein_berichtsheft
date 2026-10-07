<?php

namespace App\Data;

use App\Enums\WeekStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/** {year}/KW-NN/week.json + the days of that week. */
final class WeeklyReport
{
    /** @var Collection<string, DayReport> Mon–Fri, keyed Y-m-d (empty in list mode) */
    public Collection $days;

    /** Number of day files in the folder. */
    public int $recordedDays = 0;

    public function __construct(
        public readonly User $azubi,
        public readonly int $year,
        public readonly int $week,
        public ?int $number = null,
        public WeekStatus $status = WeekStatus::Draft,
        public ?CarbonImmutable $submittedAt = null,
        public ?CarbonImmutable $signedAt = null,
        public ?int $signedById = null,
        public ?string $signedByName = null,
        public ?array $submittedSignature = null,
        public ?array $signedSignature = null,
        public ?string $summary = null,
        public bool $exists = false,
    ) {
        $this->days = collect();
    }

    /** JSON keys → properties. If your existing weekly files use other keys, map them here. */
    public static function fromArray(User $azubi, int $year, int $week, ?array $data): self
    {
        $data ??= [];
        $date = fn (?string $v) => $v ? CarbonImmutable::parse($v) : null;

        return new self(
            azubi: $azubi,
            year: $year,
            week: $week,
            status: WeekStatus::tryFrom($data['status'] ?? '') ?? WeekStatus::Draft,
            submittedAt: $date($data['eingereicht_am'] ?? null),
            signedAt: $date($data['unterschrieben_am'] ?? null),
            signedById: $data['unterschrieben_von']['id'] ?? null,
            signedByName: $data['unterschrieben_von']['name'] ?? null,
            submittedSignature: $data['azubi_unterschrift'] ?? null,
            signedSignature: $data['ausbilder_unterschrift'] ?? null,
            summary: $data['zusammenfassung'] ?? null,
            exists: $data !== [],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'jahr'                    => $this->year,
            'kalenderwoche'           => $this->week,
            'azubi'                   => $this->azubi->name,
            'status'                  => $this->status->value,
            'eingereicht_am'          => $this->submittedAt?->toIso8601String(),
            'unterschrieben_am'       => $this->signedAt?->toIso8601String(),
            'unterschrieben_von'      => $this->signedById ? ['id' => $this->signedById, 'name' => $this->signedByName] : null,
            'azubi_unterschrift'      => $this->submittedSignature,
            'ausbilder_unterschrift'  => $this->signedSignature,
            'zusammenfassung'         => $this->summary,
        ], fn ($v) => $v !== null);
    }

    public function start(): CarbonImmutable
    {
        return CarbonImmutable::now()->setISODate($this->year, $this->week)->startOfDay();
    }

    public function end(): CarbonImmutable
    {
        return $this->start()->addDays(4);
    }

    /** "22.09. – 26.09.2026" */
    public function period(): string
    {
        return $this->start()->format('d.m.') . ' – ' . $this->end()->format('d.m.Y');
    }

    public function is(?self $other): bool
    {
        return $other !== null
            && $other->azubi->is($this->azubi)
            && $other->year === $this->year
            && $other->week === $this->week;
    }

    public function isEditable(): bool
    {
        return $this->status === WeekStatus::Draft;
    }

    public function isSigned(): bool
    {
        return $this->status === WeekStatus::Signed;
    }

    public function canSubmit(): bool
    {
        return $this->isEditable() && $this->recordedDays >= 5;
    }

    /** For route('weekly-reports.show', $week->routeParams()) */
    public function routeParams(): array
    {
        return ['user' => $this->azubi, 'year' => $this->year, 'week' => $this->week];
    }
}
