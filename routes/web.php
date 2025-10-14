<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/policy', function () {
    return view('policy');
})->name('policy');