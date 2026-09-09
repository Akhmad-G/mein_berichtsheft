@include('reports.page', [
    'title' => __('Tagesbericht') . ' — ' . ($report['date'] ?? ''),
    'formView' => 'tagesberichte.form',
    'action' => '#',
    'method' => 'GET',
    'report' => $report,
    'readonly' => true,
    'submitLabel' => '',
    'actions' => new \Illuminate\Support\HtmlString(
        view('reports.actions', [
            'canManage' => $canManage,
            'editRoute' => route('tagesberichte.edit', [
                'tagesberichte' => \App\Support\GitLabPath::encode($path),
            ]),
            'destroyRoute' => route('tagesberichte.destroy', [
                'tagesberichte' => \App\Support\GitLabPath::encode($path),
            ]),
            'deleteConfirm' => 'Tagesbericht wirklich löschen?',
        ])->render()
    ),
])