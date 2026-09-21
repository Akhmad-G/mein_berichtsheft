@props(['context' => null])

<header class="border-b border-rule bg-paper-line">
  <div class="max-w-[1280px] mx-auto px-5 min-h-[54px] flex flex-wrap items-center justify-between gap-[18px]">

    <div class="flex items-center gap-[11px]">
      <x-brand :size="21"
               :text="15.5"
      />
      <span class="w-px h-[18px] bg-rule mx-[3px]"></span>
      <span class="text-[13px] text-ink-soft whitespace-nowrap">
        {{ $context ?? auth()->user()->ausbildungsbetrieb }}
      </span>
    </div>

    <div class="flex items-center gap-2.5">
      <x-theme-toggle />
      <x-app.user-menu />
    </div>
  </div>
</header>
