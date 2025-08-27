<?php

use App\Http\Controllers\VoiceController;
use App\Http\Controllers\WhisperControlller;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/voice', [VoiceController::class, 'index']);
Route::post('/voice/text-chat', [VoiceController::class, 'handleTextWithContext']);

Route::get('/voice-whisper', [WhisperControlller::class, 'index']);
Route::post('/voice-whisper/voice-chat', [WhisperControlller::class, 'handleAudio']);
