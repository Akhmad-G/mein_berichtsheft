<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use App\Models\User;
use App\Support\GitLabPath;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WochenberichtController extends Controller
{
    protected array $wochentage = ['Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag'];
  
  /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wochenberichte.create');
    }
    
    // AJAX: Wird über die Schaltfläche „Aus Tagesberichten übernehmen“ aufgerufen
    public function uebernehmen(Request $request, GitLabServiceInterface $gitLabService)
    {
        $request->validate(['week' => 'required|string']);
        
        $weekStart = $this->parseWeekStart($request->query('week'));
        
        $tagesberichte = $gitLabService->getReportsForWeek($request->user(), $weekStart);
        
        $result = [];
        foreach ($this->wochentage as $tag) {
            $result[$tag] = [
                'taetigkeiten' => $tagesberichte[$tag]['taetigkeiten'] ?? '',
                'gelernt' => $tagesberichte[$tag]['gelernt'] ?? '',
                'probleme' => $tagesberichte[$tag]['probleme'] ?? '',
            ];
        }
        
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GitLabServiceInterface $gitLabService)
    {
        $validated = $request->validate([
            'week' => 'required|string',
            'tage' => 'required|array',
            'tage.*.taetigkeiten' => 'nullable|string',
            'tage.*.gelernt' => 'nullable|string',
            'tage.*.probleme' => 'nullable|string',
            'tage.*.ausbildungsplan' => 'nullable|string',
        ]);
        
        $user = $request->user();
        $weekStart = $this->parseWeekStart($validated['week']);
        $weekEnd = $weekStart->copy()->addDays(4);
        
        [$year, $weekNumber] = explode('-W', $validated['week']);
        
        $tage = [];
        foreach ($this->wochentage as $index => $tag) {
            $date = $weekStart->copy()->addDays($index);
            $tage[$tag] = [
                'date' => $date->format('Y-m-d'),
                'taetigkeiten' => $validated['tage'][$tag]['taetigkeiten'] ?? '',
                'gelernt' => $validated['tage'][$tag]['gelernt'] ?? '',
                'probleme' => $validated['tage'][$tag]['probleme'] ?? '',
                'ausbildungsplan' => $validated['tage'][$tag]['ausbildungsplan'] ?? '',
            ];
        }
        
        $data = [
            'berichtsnummer' => $user->nextBerichtsnummer(),
            'kalenderwoche' => "KW {$weekNumber} / {$year}",
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
            'user' => [
                'name' => $user->name,
                'ausbildungsberuf' => $user->ausbildungsberuf,
                'ausbildungsbetrieb' => $user->ausbildungsbetrieb,
            ],
            'tage' => $tage,
            'unterschriften' => [
                'azubi' => null,
                'ausbilder' => null,
            ],
            'created_at' => now()->toIso8601String(),
        ];
        
        $filename = sprintf('%s-KW%02d Wochenbericht.json', $year, (int) $weekNumber);
        
        $gitLabService->saveReport($user, $filename, $data);
        
        return redirect()->route('dashboard')->with('success', 'Wochenbericht gespeichert.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $path, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($path);
        $reportOwner = $this->authorizeReportPath($realPath);
        
        $report = $gitLabService->getReport($reportOwner, $realPath);
        
        return view('wochenberichte.show', [
            'report' => $report,
            'path' => GitLabPath::encode($realPath),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    private function parseWeekStart(mixed $week) {
        [$year, $weekNumber] = explode('-W', $week);
        
        return Carbon::now()->setISODate((int) $year, (int) $weekNumber)->startOfWeek();
    }
    
    public function sign(Request $request, string $path, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($path);
        $reportOwner = $this->authorizeReportPath($realPath);
        
        $validated = $request->validate([
            'signature' => 'required|string|starts_with:data:image/png;base64,',
        ]);
        
        $user = $request->user();
        
        if (! $user->isAzubi() && ! $user->isAusbilder()) {
            abort(403, 'Diese Rolle darf nicht unterschreiben.');
        }
        
        if ($user->isAzubi() && $user->id !== $reportOwner->id) {
            abort(403, 'Azubis dürfen nur eigene Wochenberichte unterschreiben.');
        }
        
        if ($user->isAusbilder() && $reportOwner->ausbilder_id !== $user->id) {
            abort(403, 'Ausbilder dürfen nur Wochenberichte eigener Azubis unterschreiben.');
        }
        
        $signatureKey = $user->isAusbilder() ? 'ausbilder' : 'azubi';
        
        $existing = $gitLabService->getReport($reportOwner, $realPath);
        
        $existing['unterschriften'] ??= [
            'azubi' => null,
            'ausbilder' => null,
        ];
        
        $existing['unterschriften'][$signatureKey] = [
            'name' => $user->name,
            'signed_at' => now()->format('d.m.Y H:i'),
            'image' => $validated['signature'],
        ];
        
        $filename = basename($realPath);
        
        $gitLabService->saveReport($reportOwner, $filename, $existing, 'update');
        
        return response()->json(['success' => true]);
    }
    
    public function pdf(string $path, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($path);
        $reportOwner = $this->authorizeReportPath($realPath);
        
        $report = $gitLabService->getReport($reportOwner, $realPath);
        
        $pdf = Pdf::loadView('wochenberichte.pdf', [
            'report' => $report,
            'owner' => $reportOwner,
        ])->setPaper('a4');
        
        $filename = pathinfo(basename($realPath), PATHINFO_FILENAME) . '.pdf';
        
        return $pdf->download($filename);
    }
    
    protected function authorizeReportPath(string $realPath): User
    {
        $owner = User::query()
            ->whereNotNull('gitlab_path')
            ->where('gitlab_path', '!=', '')
            ->get()
            ->first(fn (User $user) => str_starts_with($realPath, $user->gitlab_path . '/'));
        
        if (! $owner) {
            abort(403, 'Berichtsinhaber konnte nicht ermittelt werden.');
        }
        
        $currentUser = auth()->user();
        
        if ($currentUser->isAzubi() && $currentUser->id === $owner->id) {
            return $owner;
        }
        
        if ($currentUser->isAusbilder() && $owner->ausbilder_id === $currentUser->id) {
            return $owner;
        }
        
        abort(403, 'Nicht berechtigt.');
    }
}
