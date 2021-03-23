<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MP\Inspector;

//Mood Provider
Route::get('/mp/{param}', [Inspector::class, 'docks']);
Route::post('/mp/api/{param}', [Inspector::class, 'postport']);
//
Route::get('/', function () {
    return view('welcome');
});