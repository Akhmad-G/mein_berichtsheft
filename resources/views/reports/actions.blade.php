<div class="flex items-center gap-4">
  @if($canManage)
    <a href="{{ $editRoute }}"
       class="inline-flex items-center px-4 py-2 bg-white text-black rounded hover:bg-gray-200"
    > Bearbeiten </a>

    <form method="POST"
          action="{{ $destroyRoute }}"
          onsubmit="return confirm('{{ $deleteConfirm }}');"
    >
      @csrf
      @method('DELETE')

      <button type="submit"
              class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
      >
        Löschen
      </button>
    </form>
  @endif

  @isset($pdfRoute)
    <a href="{{ $pdfRoute }}"
       class="inline-flex items-center px-4 py-2 bg-white text-black rounded hover:bg-gray-200"
    > PDF herunterladen </a>
  @endisset

  <a href="{{ route('dashboard') }}"
     class="inline-block text-sm text-gray-900 dark:text-gray-100"
  > ← Zurück zum Dashboard </a>
</div>