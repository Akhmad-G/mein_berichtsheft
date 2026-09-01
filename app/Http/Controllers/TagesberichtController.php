<?php

namespace App\Http\Controllers;

use App\Contracts\GitLabServiceInterface;
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
//        dd($request->all());
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
        $report = $gitLabService->getReport(auth()->user(), $path);
        
        return view('tagesberichte.show', [
            'report' => $report,
            'path' => $path,
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
}
