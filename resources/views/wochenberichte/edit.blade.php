@include('wochenberichte.page', [
  'title' => __('Wochenbericht bearbeiten'),
  'action' => route('wochenberichte.update', ['wochenberichte' => $path]),
  'method' => 'PUT',
  'report' => $report,
  'readonly' => false,
  'autoload' => false,
  'submitLabel' => __('Wochenbericht aktualisieren'),
])