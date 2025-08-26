<?php

use App\Http\Controllers\VoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/voice', [VoiceController::class, 'index']);
Route::post('/voice/text-chat', [VoiceController::class, 'handleText']);
