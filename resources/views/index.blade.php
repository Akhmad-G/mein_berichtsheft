@extends('layouts.app')

@section('title', 'Mein Berichtsheft')

@section('content')
  <nav>
    <a href="{{ route('tagesbericht.create') }}">Neu Tagesbericht</a>
    <a href="{{ route('wochenbericht.create') }}">Neu Wochenbericht</a>
  </nav>
  
  @forelse($berichtshefte as $berichtshefte)
    <div>
      <a href="{{ route('tagesbericht.show', ['tagesbericht' => $tagesbericht->id]) }}">
        {{ $berichtsheft->title }}
      </a>
    </div>
  @endforelse


@endsection