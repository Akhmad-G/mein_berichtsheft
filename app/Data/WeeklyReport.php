<?php

namespace App\Data;

use App\Enums\WeekStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Data Transfer Object representing a weekly training report and its collection of days.
 * Stored as JSON: {year}/KW-{week}/KW-{week} Wochenbericht.json
 */
final class WeeklyReport
{
    /**
     * Daily reports collection for the week (Monday to Friday), keyed by 'YYYY-MM-DD'.
     *
     * @var Collection<string, DayReport>
     */
    public Collection $days;

    /**
     * Number of day files present in the week folder.
     *
     * @var int
     */
    public int $recordedDays = 0;

    /**
     * @param User $azubi The apprentice to whom this report belongs.
     * @param int $year ISO calendar year.
     * @param int $week ISO calendar week number (1-53).
     * @param int|null $number Consecutive report number (Berichtsnummer).
     * @param WeekStatus $status Current review / approval status.
     * @param CarbonImmutable|null $submittedAt Timestamp when the report was submitted by apprentice.
     * @param CarbonImmutable|null $signedAt Timestamp when the report was approved / signed by trainer.
     * @param int|null $signedById User ID of trainer who signed.
     * @param string|null $signedByName Full name of trainer who signed.
     * @param array<string, mixed>|null $submittedSignature Digital signature data of the apprentice.
     * @param array<string, mixed>|null $signedSignature Digital signature data of the trainer.
     * @param string|null $summary Summary or review note for the week.
     * @param bool $exists Whether the weekly report file exists in storage.
     */
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

    /**
     * Create a WeeklyReport instance from storage array data.
     *
     * @param User $azubi
     * @param int $year
     * @param int $week
     * @param array<string, mixed>|null $data
     * @return self
     */
    public static function fromArray(User $azubi, int $year, int $week, ?array $data): self
    {
        $data ??= [];
        $date = fn (?string $v) => $v ? CarbonImmutable::parse($v) : null;

        return new self(
            azubi: $azubi,
            year: $year,
            week: $week,
            number: $data['berichtsnummer'] ?? null,
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

    /**
     * Convert the weekly report into an array suitable for JSON persistence.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'jahr'                    => $this->year,
            'kalenderwoche'           => $this->week,
            'azubi'                   => $this->azubi->name,
            'berichtsnummer'          => $this->number,
            'status'                  => $this->status->value,
            'eingereicht_am'          => $this->submittedAt?->toIso8601String(),
            'unterschrieben_am'       => $this->signedAt?->toIso8601String(),
            'unterschrieben_von'      => $this->signedById ? ['id' => $this->signedById, 'name' => $this->signedByName] : null,
            'azubi_unterschrift'      => $this->submittedSignature,
            'ausbilder_unterschrift'  => $this->signedSignature,
            'zusammenfassung'         => $this->summary,
        ], fn ($v) => $v !== null);
    }

    /**
     * Get the start date (Monday) of the week.
     *
     * @return CarbonImmutable
     */
    public function start(): CarbonImmutable
    {
        return CarbonImmutable::now()->setISODate($this->year, $this->week)->startOfDay();
    }

    /**
     * Get the end date (Friday) of the work week.
     *
     * @return CarbonImmutable
     */
    public function end(): CarbonImmutable
    {
        return $this->start()->addDays(4);
    }

    /**
     * Formatted date range string for UI display (e.g., "22.09. – 26.09.2026").
     *
     * @return string
     */
    public function period(): string
    {
        return $this->start()->format('d.m.') . ' – ' . $this->end()->format('d.m.Y');
    }

    /**
     * Check if another report instance represents the same apprentice, year, and week.
     *
     * @param self|null $other
     * @return bool
     */
    public function is(?self $other): bool
    {
        return $other !== null
            && $other->azubi->is($this->azubi)
            && $other->year === $this->year
            && $other->week === $this->week;
    }

    /**
     * Check if the weekly report can currently be edited by the apprentice.
     *
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->status === WeekStatus::Draft;
    }

    /**
     * Check if the weekly report has already been signed by a trainer.
     *
     * @return bool
     */
    public function isSigned(): bool
    {
        return $this->status === WeekStatus::Signed;
    }

    /**
     * Determine whether the report satisfies requirements for submission (draft state & 5 recorded days).
     *
     * @return bool
     */
    public function canSubmit(): bool
    {
        return $this->isEditable() && $this->recordedDays >= 5;
    }

    /**
     * Generate route parameters array for linking to this report.
     *
     * @return array{user: User, year: int, week: int}
     */
    public function routeParams(): array
    {
        return ['user' => $this->azubi, 'year' => $this->year, 'week' => $this->week];
    }
}
