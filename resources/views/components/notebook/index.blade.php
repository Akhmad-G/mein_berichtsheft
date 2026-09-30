@props(['tabs' => true])

{{-- Open notebook: left page = navigation/overview, right page = sheet.
     Without tabs (profile) all corners are rounded. --}}
<div
  @class([
    'min-w-0 grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_minmax(0,1.12fr)] overflow-hidden',
    'border border-rule bg-paper shadow-[0_20px_44px_-30px_rgba(0,0,0,.35)]',
    $tabs ? 'rounded-[0_10px_10px_10px]' : 'rounded-[10px]',
  ])
>
  <div class="border-b md:border-b-0 md:border-r border-rule bg-paper-line flex flex-col">
    {{ $left }}
  </div>

  <div class="flex flex-col">
    {{ $right }}
  </div>
</div>
