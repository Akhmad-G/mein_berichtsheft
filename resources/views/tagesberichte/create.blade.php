@include('reports.page', [
  'title' => __('Neuer Tagesbericht'),
  'formView' => 'tagesberichte.form',
  'action' => route('tagesberichte.store'),
  'method' => 'POST',
  'report' => [],
  'readonly' => false,
  'submitLabel' => __('Tagesbericht speichern'),
])