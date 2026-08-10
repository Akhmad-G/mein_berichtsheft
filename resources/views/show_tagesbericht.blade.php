@extends('layouts.app')

@section('title', $bericht->datum . ' Tagesbericht')

@section('content')
  <div>
    <p>Ausbildungsnachweis_Nummer: {{ $bericht->ausbildungsnachweis_nummer }}</p>
    <p>Datum: {{ $bericht->datum }}</p>
    <p>Wochentag: {{ $bericht->wochentag }}</p>
    <p>Name: {{ $bericht->name }}</p>
    <p>Ausbildungsberuf: {{ $bericht->ausbildungsberuf }}</p>
    <p>Betrieb: {{ $bericht->betrieb }}</p>
    <p>Abteilung: {{ $bericht->abteilung }}</p>
    <p>Ausbildungsjahr: {{ $bericht->ausbildungsjahr }}</p>
    <p>Ausbildungswoche: {{ $bericht->ausbildungswoche }}</p>
  </div>
  <div>
    <h2>Tätigkeiten</h2>
    <p>{{ $bericht->taetigkeiten }}</p>
  </div>
  <div>
    <h2>Was habe ich gelernt?</h2>
    <p>{{ $bericht->gelernt }}</p>
  </div>
  <div>
    <h2>Besonderes Ereignisse / Probleme</h2>
    <p>{{ $bericht->probleme }}</p>
  </div>
  

  


@endsection