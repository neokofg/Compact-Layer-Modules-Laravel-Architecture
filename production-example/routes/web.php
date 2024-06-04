<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return "all good";
});
Route::view('/docs', 'scribe.index')->name('public_docs');
