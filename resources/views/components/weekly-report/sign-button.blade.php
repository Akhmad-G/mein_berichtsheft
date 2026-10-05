@props(['week']) {{-- App\Data\WeeklyReport --}}

{{-- Ausbilder signs (signed tone), Azubi submits (ink) --}}@use('App\Enums\WeekStatus')@php $user = auth()->user(); @endphp

@if ($user->isAusbilder())
  <form method="POST"
        action="{{ route('weekly-reports.sign', $week->routeParams()) }}"
  >
    @csrf
    <button type="submit"
            @disabled($week->status !== WeekStatus::Submitted)class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-report text-paper
                       hover:opacity-90 disabled:opacity-50 cursor-pointer disabled:cursor-default"
    >
      {{ $week->isSigned() ? 'Unterschrieben' : 'Unterschreiben' }}
    </button>
  </form>
@else
  <form method="POST"
        action="{{ route('weekly-reports.submit', $week->routeParams()) }}"
  >
    @csrf
    <button type="submit"
            @disabled(! $week->canSubmit())class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-ink text-paper
                       hover:opacity-90 disabled:opacity-50 cursor-pointer disabled:cursor-default"
    >
      {{ match ($week->status) {
          WeekStatus::Draft     => 'Zur Unterschrift geben',
          WeekStatus::Submitted => 'Eingereicht',
          WeekStatus::Signed    => 'Unterschrieben',
      } }}
    </button>
  </form>
@endif
