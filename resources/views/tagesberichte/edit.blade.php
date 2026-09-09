@include('tagesberichte.page', [
  'title' => __('Tagesbericht bearbeiten'),
  'action' => route('tagesberichte.update', ['tagesberichte' => $path]),
  'method' => 'PUT',
  'report' => $report,
  'readonly' => false,
  'submitLabel' => __('Tagesbericht aktualisieren'),
])