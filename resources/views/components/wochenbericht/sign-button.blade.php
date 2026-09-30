@props(['week'])

@php $user = auth()->user(); @endphp

@if ($woche->path)
  <form method="POST"
        action="{{ route('wochenberichte.sign', ['path' => $woche->path]) }}"
  >
    @csrf

    <input type="hidden"
           name="signature"
           value="data:image/png;base64,"
    >

    <button type="submit"
            @disabled($user->isAusbilder() ? $woche->istUnterschrieben : ! $woche->kannEinreichen)
            class="text-[13px] font-medium px-[15px] py-[7px] rounded-md {{ $user->isAusbilder() ? 'bg-bericht' : 'bg-ink' }} text-paper
                   hover:opacity-90 disabled:opacity-50 cursor-pointer"
    >
      @if ($woche->istUnterschrieben)
        Unterschrieben
      @elseif ($user->isAusbilder())
        Unterschreiben
      @else
        Zur Unterschrift geben
      @endif
    </button>
  </form>
@endif

{{--@props(['woche'])--}}

{{--@php $user = auth()->user(); @endphp--}}

{{--@if ($user->isAusbilder())--}}
{{--  <form method="POST"--}}
{{--        action="{{ route('wochenbericht.unterschreiben', $woche) }}"--}}
{{--  >--}}
{{--    @csrf--}}
{{--    <button type="submit"--}}
{{--            @disabled($woche->istUnterschrieben)class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-bericht text-paper--}}
{{--                       hover:opacity-90 disabled:opacity-50 cursor-pointer"--}}
{{--    >--}}
{{--      {{ $woche->istUnterschrieben ? 'Unterschrieben' : 'Unterschreiben' }}--}}
{{--    </button>--}}
{{--  </form>--}}
{{--@else--}}
{{--  <form method="POST"--}}
{{--        action="{{ route('wochenbericht.einreichen', $woche) }}"--}}
{{--  >--}}
{{--    @csrf--}}
{{--    <button type="submit"--}}
{{--            @disabled(! $woche->kannEinreichen)class="text-[13px] font-medium px-[15px] py-[7px] rounded-md bg-ink text-paper--}}
{{--                       hover:opacity-90 disabled:opacity-50 cursor-pointer"--}}
{{--    >--}}
{{--      {{ $woche->istUnterschrieben ? 'Unterschrieben' : 'Zur Unterschrift geben' }}--}}
{{--    </button>--}}
{{--  </form>--}}
{{--@endif--}}
