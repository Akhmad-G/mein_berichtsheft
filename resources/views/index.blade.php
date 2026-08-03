@extends('layouts.app')

@section('title', 'Mein Berichtsheft')

@section('content')
  <nav>
    <a href="{{ route('berichtsheft.create') }}">Neu Tagesbericht</a>
{{--    <a href="/">Neu Wochenbericht</a>--}}
  </nav>


@endsection