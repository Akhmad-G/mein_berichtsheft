@include('reports.page', [
  'title' => __('Neuer Wochenbericht'),
  'formView' => 'wochenberichte.form',
  'action' => route('wochenberichte.store'),
  'method' => 'POST',
  'report' => [],
  'readonly' => false,
  'autoload' => true,
  'submitLabel' => __('Wochenbericht speichern'),
])