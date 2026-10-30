<?php

namespace App\Http\Requests;

use App\Enums\DayType;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Form request for creating, updating, or range-filling daily training reports.
 */
class DayReportRequest extends FormRequest
{
    /**
     * Maximum allowed date range in days for bulk entries (e.g. 2 months).
     */
    private const MAX_RANGE_DAYS = 62;

    /**
     * Determine if the user is authorized to make this request (Azubis only).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->isAzubi();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $work  = $this->input('type') === DayType::Work->value;
        $range = ! $work && $this->boolean('range');

        return [
            'type'             => ['required', Rule::enum(DayType::class)],

            // work day
            'activities'       => [$work ? 'nullable' : 'exclude', 'string', 'max:600'],
            'duration'         => [$work ? 'nullable' : 'exclude', 'string', 'max:20'],
            'department'       => [$work ? 'nullable' : 'exclude', 'string', 'max:255'],
            'learning_steps'   => [$work ? 'nullable' : 'exclude', 'array'],
            'learning_steps.*' => ['nullable', 'string', 'max:50'],

            // absence
            'note'             => [$work ? 'exclude' : 'nullable', 'string', 'max:200'],
            'range'            => ['nullable', 'boolean'],
            'from'             => [$range ? 'required' : 'exclude', 'date_format:Y-m-d'],
            'to'               => [$range ? 'required' : 'exclude', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    /**
     * Configure the "after" validation callbacks.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->hasAny(['from', 'to']) || ! $this->filled(['from', 'to'])) {
                return;
            }
            if (CarbonImmutable::parse($this->input('from'))->diffInDays($this->input('to')) > self::MAX_RANGE_DAYS) {
                $validator->errors()->add('to', 'Der Zeitraum darf höchstens zwei Monate umfassen.');
            }
        }];
    }

    /**
     * Custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to.after_or_equal' => '„Bis“ darf nicht vor „Von“ liegen.',
        ];
    }
}
