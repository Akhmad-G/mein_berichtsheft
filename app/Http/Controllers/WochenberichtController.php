<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use App\Support\GitLabPath;
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
        logger()->info('Wochenbericht sign route reached', [
            'path' => $path,
            'decoded' => GitLabPath::decode($path),
        ]);
        
        $realPath = GitLabPath::decode($path);
        $this->authorizeOwnPath($realPath);
        
        $report = $gitLabService->getReport(auth()->user(), $realPath);
        
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
        $this->authorizeOwnPath($realPath);
        
        $validated = $request->validate([
            'signature' => 'required|string|starts_with:data:image/png;base64,',
        ]);
        
        $user = $request->user();
        
        $existing = $gitLabService->getReport($user, $realPath);
        
        $existing['unterschriften']['azubi'] = [
            'name' => $user->name,
            'signed_at' => now()->format('d.m.Y H:i'),
            'image' => $validated['signature'],
        ];
        
        $filename = basename($realPath);
        
        $gitLabService->saveReport($user, $filename, $existing, 'update');
        
        return response()->json(['success' => true]);
    }
    
    protected function authorizeOwnPath(string $realPath): void
    {
        if (! str_starts_with($realPath, auth()->user()->gitlab_path . '/')) {
            abort(403, 'Nicht berechtigt.');
        }
    }
}
