@include('reports.page', [
  'title' => __('Wochenbericht bearbeiten'),
  'formView' => 'wochenberichte.form',
  'action' => route('wochenberichte.update', ['wochenberichte' => $path]),
  'method' => 'PUT',
  'report' => $report,
  'readonly' => false,
  'autoload' => false,
  'submitLabel' => __('Wochenbericht aktualisieren'),
])