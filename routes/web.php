<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("berichtshefte.index");
});

Route::get('/berichtshefte/', function () {
    return view('index');
})->name("berichtshefte.index");

Route::view()