@props(['week']) {{-- App\Data\WeeklyReport --}}

@use('App\Enums\WeekStatus')

@php
  $user = auth()->user();

  $canOpenSignature = $user->isAusbilder()
      ? $week->status === WeekStatus::Submitted
      : $week->canSubmit();

  $signAction = $user->isAusbilder()
      ? route('weekly-reports.sign', $week->routeParams())
      : route('weekly-reports.submit', $week->routeParams());

  $buttonLabel = $user->isAusbilder()
      ? 'Unterschreiben'
      : 'Zur Unterschrift geben';

  $visibleButtonLabel = $week->status === WeekStatus::Submitted && ! $user->isAusbilder()
      ? 'Eingereicht'
      : $buttonLabel;
@endphp

@if ($week->isSigned())
  <x-button type="button"
            variant="primary"
            size="sm"
            disabled
            class="opacity-50 cursor-default"
  >
    Unterschrieben
  </x-button>
@else
  <div data-signature-modal>
    <x-button type="button"
              variant="primary"
              size="sm"
              data-signature-open
              :disabled="! $canOpenSignature"
              class="disabled:opacity-50 disabled:cursor-default"
    >
      {{ $visibleButtonLabel }}
    </x-button>

    <div data-signature-backdrop
         hidden
         class="fixed inset-0 z-50 flex items-center justify-center bg-ink/35 px-4"
    >
      <div class="w-full max-w-[560px] rounded-xl border border-rule bg-paper shadow-xl">
        <div class="flex items-start justify-between gap-4 border-b border-rule px-5 py-4">
          <div>
            <h3 class="font-display text-[19px] text-ink">Unterschrift setzen</h3>
            <p class="mt-1 text-[12.5px] text-ink-soft">
              Bitte unterschreiben Sie den Wochenbericht KW {{ $week->week }}. </p>
          </div>

          <button type="button"
                  data-signature-close
                  class="text-[22px] leading-none text-ink-soft hover:text-ink"
                  aria-label="Schließen"
          >
            ×
          </button>
        </div>

        <form method="POST"
              action="{{ $signAction }}"
              class="px-5 py-5"
        >
          @csrf

          <input type="hidden"
                 name="signature"
                 data-signature-input
          >

          <div class="rounded-lg border border-rule bg-paper-raised p-3">
            <canvas data-signature-pad
                    width="900"
                    height="260"
                    class="block h-[180px] w-full cursor-crosshair rounded-md bg-paper"
            ></canvas>
          </div>

          <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
            <x-button type="button"
                      variant="secondary"
                      size="sm"
                      data-signature-clear
            >
              Feld leeren
            </x-button>

            <div class="flex gap-2">
              <x-button type="button"
                        variant="secondary"
                        size="sm"
                        data-signature-close
              >
                Abbrechen
              </x-button>

              <x-button type="submit"
                        variant="primary"
                        size="sm"
                        data-signature-submit
                        disabled
                        class="disabled:opacity-50 disabled:cursor-default"
              >
                Unterschreiben
              </x-button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif