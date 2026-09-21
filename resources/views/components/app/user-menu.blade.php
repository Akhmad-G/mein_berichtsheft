@php $user = auth()->user(); @endphp

<div class="relative"
     data-user-menu
>
  <button type="button"
          data-user-menu-button
          class="flex items-center gap-2 px-3 py-[7px] border border-rule rounded-md bg-paper
               hover:bg-paper-raised aria-expanded:bg-paper-raised cursor-pointer transition-colors"
          aria-expanded="false"
  >
    <span class="text-[13.5px]">{{ $user->vorname }} {{ $user->nachname }}</span>
    <span class="text-[11.5px] text-ink-soft">{{ $user->isAusbilder() ? 'Ausbilder' : 'Azubi' }}</span>

    <svg viewBox="0 0 12 12"
         class="w-2.5 h-2.5 shrink-0 text-ink-soft transition-transform duration-150"
         data-user-menu-chevron
    >
      <path d="M2.5 4.5 L6 8 L9.5 4.5"
            fill="none"
            stroke="currentColor"
            stroke-width="1.6"
            stroke-linecap="round"
            stroke-linejoin="round"
      />
    </svg>
  </button>

  <div data-user-menu-panel
       hidden
       class="absolute top-[calc(100%+6px)] right-0 min-w-[190px] z-20 flex flex-col overflow-hidden
               border border-rule rounded-md bg-paper shadow-[0_14px_30px_-18px_rgba(0,0,0,.45)]"
  >
    <a href="{{ route('profile.edit') }}"
       class="px-3.5 py-2.5 text-[13.5px] border-b border-rule hover:bg-paper-line"
    >Profil</a>

    <form method="POST"
          action="{{ route('logout') }}"
    >
      @csrf
      <button type="submit"
              class="w-full text-left px-3.5 py-2.5 text-[13.5px] text-stamp hover:bg-paper-line cursor-pointer"
      >
        Abmelden
      </button>
    </form>
  </div>
</div>
