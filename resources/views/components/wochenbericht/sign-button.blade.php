@props(['woche'])

@php $user = auth()->user(); @endphp

@if ($user->istAusbilder())
  <form method="POST"
        action="{{ route('wochenbericht.unterschreiben', $woche) }}"
  >
    @csrf
    <button type="submit"
            @disabled($woche->istUnterschrieben)class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-bericht text-paper
                       hover:opacity-90 disabled:opacity-50 cursor-pointer"
    >
      {{ $woche->istUnterschrieben ? 'Unterschrieben' : 'Unterschreiben' }}
    </button>
  </form>
@else
  <form method="POST"
        action="{{ route('wochenbericht.einreichen', $woche) }}"
  >
    @csrf
    <button type="submit"
            @disabled(! $woche->kannEinreichen)class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-ink text-paper
                       hover:opacity-90 disabled:opacity-50 cursor-pointer"
    >
      {{ $woche->istUnterschrieben ? 'Unterschrieben' : 'Zur Unterschrift geben' }}
    </button>
  </form>
@endif
