@php
 use Illuminate\Support\HtmlString;
 use App\Support\GitLabPath;
@endphp

@include('reports.page', [
    'title' => __('Tagesbericht') . ' — ' . ($report['date'] ?? ''),
    'formView' => 'tagesberichte.form',
    'action' => '#',
    'method' => 'GET',
    'report' => $report,
    'readonly' => true,
    'submitLabel' => '',
    'actions' => new HtmlString(
        view('reports.actions', [
            'canManage' => $canManage,
            'editRoute' => route('tagesberichte.edit', [
                'tagesberichte' => GitLabPath::encode($path),
            ]),
            'destroyRoute' => route('tagesberichte.destroy', [
                'tagesberichte' => GitLabPath::encode($path),
            ]),
            'deleteConfirm' => 'Tagesbericht wirklich löschen?',
        ])->render()
    ),
])