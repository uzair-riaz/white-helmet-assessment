<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Welcome to the Task Management API', 
        'documentation' => 'See README.md for API documentation'
    ]);
});
