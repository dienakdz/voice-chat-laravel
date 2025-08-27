<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhisperControlller extends Controller
{
    public function index()
    {
        return view('voice-whisper');
    }

    public function handleAudio(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:wav,mp3,webm',
        ]);
        if (!$request->hasFile('audio')) {
            return response()->json(['error' => 'File không được gửi'], 400);
        }

        $file = $request->file('audio');

        // Lưu vào storage/app/audio/ với tên temp_audio.wav
        $filePath = $file->storeAs('audio', 'temp_audio.wav');

        if (!$filePath) {
            return response()->json(['error' => 'File không được lưu'], 500);
        }

        $fullPath = storage_path('app/' . $filePath);
        $dir = storage_path('app/audio');
        dd(is_writable($dir), $dir);

        dd(file_exists($fullPath), $fullPath);


        // Gọi Python Whisper
        $pythonPath = 'D:\\pyCharmProject\\whisper-open-ai\\.venv\\Scripts\\python.exe';
        $pythonScript = storage_path('whisper/transcribe.py');
        $pythonScript = str_replace('\\', '/', $pythonScript);

        $cmd = escapeshellarg($pythonPath) . " " . escapeshellarg($pythonScript) . " " . escapeshellarg($fullPath);

        $output = null;
        $return_var = null;
        exec($cmd, $output, $return_var);

        dd($output, $return_var);

        $userText = implode("\n", $output);


        if (!$userText) {
            return response()->json([
                'ai_text' => 'Không nhận dạng được âm thanh.',
            ]);
        }

        // ----- 2. Gửi text + context lên Gemini API -----
        $context = $request->input('context', []);
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are TMA AI assistant. Respond professionally and helpfully in TMA style.'
            ]
        ];
        if (!empty($context) && is_array($context)) {
            $messages = array_merge($messages, $context);
        }
        $messages[] = [
            'role' => 'user',
            'content' => $userText
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions', [
            'model' => 'gemini-2.5-flash',
            'messages' => $messages,
        ]);

        $json = $response->json();

        if (!isset($json['choices'][0]['message']['content'])) {
            $aiText = $json['error']['message'] ?? 'Lỗi khi gọi Gemini API';
        } else {
            $aiText = $json['choices'][0]['message']['content'];
        }

        // ----- 3. Trả về JSON -----
        return response()->json([
            'ai_text' => $aiText,
            'user_text' => $userText,
            'messages' => array_merge($messages, [
                ['role' => 'assistant', 'content' => $aiText]
            ]),
        ]);
    }
}
