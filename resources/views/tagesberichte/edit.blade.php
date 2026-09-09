@include('reports.page', [
  'title' => __('Tagesbericht bearbeiten'),
  'formView' => 'tagesberichte.form',
  'action' => route('tagesberichte.update', ['tagesberichte' => $path]),
  'method' => 'PUT',
  'report' => $report,
  'readonly' => false,
  'submitLabel' => __('Tagesbericht aktualisieren'),
])