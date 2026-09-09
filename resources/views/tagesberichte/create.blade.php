@include('tagesberichte.page', [
  'title' => __('Neuer Tagesbericht'),
  'action' => route('tagesberichte.store'),
  'method' => 'POST',
  'report' => [],
  'readonly' => false,
  'submitLabel' => __('Tagesbericht speichern'),
])
