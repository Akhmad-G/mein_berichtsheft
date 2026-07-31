<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('https://mein-berichtsheft.ddev.artif.dev/', function () {
    return view('welcome');
});

