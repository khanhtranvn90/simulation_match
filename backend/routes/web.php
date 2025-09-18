<?php

use Illuminate\Support\Facades\Route;

require base_path('routes/test.php');

Route::get('/', function () {
    return view('welcome');
});
