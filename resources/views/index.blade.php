@extends('layouts.app')

@section('title', 'Mein Berichtsheft')

@section('content')
  <nav>
    <a href="{{ route('tagesbericht.create') }}">Neu Tagesbericht</a>
    <a href="{{ route('wochenbericht.create') }}">Neu Wochenbericht</a>
  </nav>


@endsection