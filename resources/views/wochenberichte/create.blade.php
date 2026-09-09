@include('wochenberichte.page', [
  'title' => __('Neuer Wochenbericht'),
  'action' => route('wochenberichte.store'),
  'method' => 'POST',
  'report' => [],
  'readonly' => false,
  'autoload' => true,
  'submitLabel' => __('Wochenbericht speichern'),
])