<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      Wochenbericht — {{ $report['kalenderwoche'] ?? $report['week_label'] ?? '' }}
    </h2>
  </x-slot>
  
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
      
      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        @foreach (['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'] as $tag)
          @if (! empty($report['tage'][$tag]))
            <div class="mb-6">
              <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $tag }}</span>
              <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['taetigkeiten'] ?? '' }}</p>
              <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['gelernt'] ?? '' }}</p>
              <p class="whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $report['tage'][$tag]['probleme'] ?? '' }}</p>
            </div>
          @endif
        @endforeach
      </div>
      
      <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @foreach ([
            'azubi' => 'Unterschrift Azubi',
            'ausbilder' => 'Unterschrift Ausbilder',
          ] as $signatureKey => $signatureLabel)
            @php
              $signature = $report['unterschriften'][$signatureKey] ?? null;
              $canSign = (
                ($signatureKey === 'azubi' && auth()->user()->isAzubi())
                || ($signatureKey === 'ausbilder' && auth()->user()->isAusbilder())
              ) && empty($signature);
            @endphp
            
            <div class="p-4 border border-gray-700 rounded">
              <h3 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">
                {{ $signatureLabel }}
              </h3>
              
              @if (! empty($signature))
                <p class="text-sm text-gray-400">
                  Unterschrieben von {{ $signature['name'] }} am {{ $signature['signed_at'] }}
                </p>
                <img src="{{ $signature['image'] }}"
                     class="bg-white rounded mt-3"
                     style="max-width: 300px;">
              @elseif ($canSign)
                <canvas id="signature-pad" width="500" height="150"
                        class="bg-white rounded cursor-crosshair max-w-full"></canvas>
                
                <div class="flex gap-2 mt-2">
                  <button type="button" id="clear-signature" class="px-3 py-1 bg-gray-700 rounded">
                    Löschen
                  </button>
                  <button type="button" id="save-signature" class="px-3 py-1 bg-white text-black rounded">
                    Unterschreiben
                  </button>
                </div>
              @else
                <p class="text-sm text-gray-400">
                  Noch nicht unterschrieben.
                </p>
              @endif
            </div>
          @endforeach
        </div>
        
        @if (
          (auth()->user()->isAzubi() && empty($report['unterschriften']['azubi']))
          || (auth()->user()->isAusbilder() && empty($report['unterschriften']['ausbilder']))
        )
          <script>
            document.addEventListener('DOMContentLoaded', function () {
              const canvas = document.getElementById('signature-pad');

              if (!canvas) {
                return;
              }

              const ctx = canvas.getContext('2d');
              let drawing = false;
              let hasDrawing = false;

              ctx.strokeStyle = '#000';
              ctx.lineWidth = 2;
              ctx.lineCap = 'round';

              function getPos(e) {
                const rect = canvas.getBoundingClientRect();
                const point = e.touches ? e.touches[0] : e;

                return {
                  x: (point.clientX - rect.left) * (canvas.width / rect.width),
                  y: (point.clientY - rect.top) * (canvas.height / rect.height),
                };
              }

              function start(e) {
                drawing = true;
                hasDrawing = true;

                const pos = getPos(e);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);

                e.preventDefault();
              }

              function move(e) {
                if (!drawing) {
                  return;
                }

                const pos = getPos(e);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();

                e.preventDefault();
              }

              function stop() {
                drawing = false;
              }

              canvas.addEventListener('mousedown', start);
              canvas.addEventListener('mousemove', move);
              canvas.addEventListener('mouseup', stop);
              canvas.addEventListener('mouseleave', stop);
              canvas.addEventListener('touchstart', start);
              canvas.addEventListener('touchmove', move);
              canvas.addEventListener('touchend', stop);

              document.getElementById('clear-signature').addEventListener('click', function () {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                hasDrawing = false;
              });

              document.getElementById('save-signature').addEventListener('click', async function () {
                if (!hasDrawing) {
                  alert('Bitte zuerst unterschreiben.');
                  return;
                }

                const dataUrl = canvas.toDataURL('image/png');

                const response = await fetch('{{ route('wochenberichte.sign', ['path' => $path]) }}', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                  },
                  body: JSON.stringify({ signature: dataUrl }),
                });

                if (response.ok) {
                  window.location.reload();
                } else {
                  alert('Unterschrift konnte nicht gespeichert werden.');
                }
              });
            });
          </script>
        @endif
      </div>
      
      <div class="flex justify-start">
        <a href="{{ route('wochenberichte.pdf', ['path' => $path]) }}"
           class="inline-flex items-center px-4 py-2 bg-white text-black rounded hover:bg-gray-200">
          PDF herunterladen
        </a>
      </div>
    </div>
    
    <a href="{{ route('dashboard') }}" class="inline-block mt-6 text-sm text-gray-900 dark:text-gray-100 hover:text-white">
      ← Zurück zum Dashboard
    </a>
  </div>
</x-app-layout>