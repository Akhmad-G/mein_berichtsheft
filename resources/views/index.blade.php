@extends('layouts.app')

@section('title', 'Mein Berichtsheft')

@section('content')
  <nav>
    <a href="{{ route('create.tagesbericht') }}">Neuer Tagesbericht</a>
    <a href="{{ route('create.wochenbericht') }}">Neuer Wochenbericht</a>
  </nav>
  
  @forelse($berichtshefte as $berichtsheft)
    <div>
      <a href="{{ route('berichtshefte.show', ['type' => $berichtsheft->type, 'id' => $berichtsheft->id]) }}">
        {{ $berichtsheft->title }}
      </a>
    </div>
  @empty
    <p>Keine Berichtshefte</p>
  @endforelse


@endsection