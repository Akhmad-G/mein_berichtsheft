<?php

namespace App\Http\Requests;

use App\Enums\DayType;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DayReportRequest extends FormRequest
{
    private const MAX_RANGE_DAYS = 62;

    public function authorize(): bool
    {
        return $this->user()->isAzubi();
    }

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

    public function messages(): array
    {
        return [
            'to.after_or_equal' => '„Bis“ darf nicht vor „Von“ liegen.',
        ];
    }
}
