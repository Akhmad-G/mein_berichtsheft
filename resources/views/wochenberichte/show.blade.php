@include('reports.page', [
  'title' => __('Wochenbericht') . ' — ' . ($report['kalenderwoche'] ?? $report['week_label'] ?? ''),
  'formView' => 'wochenberichte.form',
  'action' => '#',
  'method' => 'GET',
  'report' => $report,
  'readonly' => true,
  'autoload' => false,
  'submitLabel' => '',
  'signatures' => new \Illuminate\Support\HtmlString(
    view('wochenberichte.signatures', [
      'report' => $report,
      'path' => $path,
    ])->render()
  ),
  'actions' => new \Illuminate\Support\HtmlString(
    view('reports.actions', [
      'canManage' => $canManage,
      'editRoute' => route('wochenberichte.edit', [
        'wochenberichte' => $path,
      ]),
      'destroyRoute' => route('wochenberichte.destroy', [
        'wochenberichte' => $path,
      ]),
      'deleteConfirm' => 'Wochenbericht wirklich löschen?',
      'pdfRoute' => route('wochenberichte.pdf', [
        'path' => $path,
      ]),
    ])->render()
  ),
])