<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => 'Task Management API',
        'documentation' => 'https://github.com/WashingtonSteve/Portifolio#readme',
    ]);
});
