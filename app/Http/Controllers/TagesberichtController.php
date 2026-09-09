<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
use App\Support\GitLabPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TagesberichtController extends Controller
{
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
    public function create(Request $request)
    {
        if (! $request->user()->isAzubi()) {
            abort(403, 'Nur Azubis dürfen Tagesberichte erstellen.');
        }
        
        return view('tagesberichte.create', [
          'user' => $request->user(),
          'ausbildungsbeginn' => auth()->user()->ausbildungsbeginn?->format('Y-m-d'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GitLabServiceInterface $gitLabService)
    {
        if (! $request->user()->isAzubi()) {
            abort(403, 'Nur Azubis dürfen Tagesberichte erstellen.');
        }
        
      $tagesbericht = $request->validate([
        'date' => 'required|date',
        'wochentag' => 'required|string',
        'ausbildungsjahr' => 'required|numeric',
        'ausbildungswoche' => ['required', 'string', 'max:255'],
        'taetigkeiten' => 'required',
        'gelernt' => '',
        'probleme' => '',
      ]);
//      dump($tagesbericht);
      
      $filename = $tagesbericht['date'] . ' Tagesbericht.json';
      
//      To save locally in storage/app/private:
//      Storage::put($filename, json_encode($tagesbericht, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
      
      $gitLabService->saveReport($request->user(), $filename, $tagesbericht);
      
      return redirect()->route('dashboard')->with('success', 'Tagesbericht gespeichert.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $path, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($path);
        
        $report = $gitLabService->getReport(auth()->user(), $realPath);
        
        $canManage = auth()->user()->isAzubi();
        
        return view('tagesberichte.show', [
            'report' => $report,
            'path' => $realPath,
            'canManage' => $canManage,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($id);
        
        $report = $gitLabService->getReport(auth()->user(), $realPath);
        
        if (! auth()->user()->isAzubi()) {
            abort(403, 'Nur Azubis dürfen Tagesberichte bearbeiten.');
        }
        
        return view('tagesberichte.edit', [
            'report' => $report,
            'path' => $id,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, GitLabServiceInterface $gitLabService)
    {
        $realPath = GitLabPath::decode($id);
        
        if (! auth()->user()->isAzubi()) {
            abort(403, 'Nur Azubis dürfen Tagesberichte bearbeiten.');
        }
        
        $tagesbericht = $request->validate([
            'date' => 'required|date',
            'wochentag' => 'required|string',
            'ausbildungsjahr' => 'required|numeric',
            'ausbildungswoche' => ['required', 'string', 'max:255'],
            'taetigkeiten' => 'required',
            'gelernt' => 'nullable|string',
            'probleme' => 'nullable|string',
        ]);
        
        $filename = basename($realPath);
        
        $gitLabService->saveReport($request->user(), $filename, $tagesbericht, 'update');
        
        return redirect()
            ->route('tagesberichte.show', ['path' => $id])
            ->with('success', 'Tagesbericht aktualisiert.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, GitLabServiceInterface $gitLabService   )
    {
        $realPath = GitLabPath::decode($id);
        
        if (! auth()->user()->isAzubi()) {
            abort(403, 'Nur Azubis dürfen Tagesberichte bearbeiten.');
        }
        
        $gitLabService->deleteReport(auth()->user(), $realPath);
        
        return redirect()
            ->route('dashboard')
            ->with('success', 'Tagesbericht gelöscht.');
    }
}
