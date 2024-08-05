<?php

use App\Http\Controllers\Api\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::post('/encode', [ShortUrlController::class, 'encode']);
Route::post('/decode', [ShortUrlController::class, 'decode']);
