<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VoiceController extends Controller
{
    public function index()
    {
        return view('voice');
    }

    public function handleText(Request $request)
    {
        $userText = $request->input('text');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions', [
            'model' => 'gemini-2.5-flash',
            'messages' => [
                ['role' => 'user', 'content' => $userText],
            ],
        ]);

        $json = $response->json();

        if (!isset($json['choices'][0]['message']['content'])) {
            $answer = $json['error']['message'] ?? 'Lỗi khi gọi Gemini API';
        } else {
            $answer = $json['choices'][0]['message']['content'];
        }

        return response()->json([
            'ai_text'    => $answer,
        ]);
    }

    public function handleTextWithContext(Request $request)
    {
        $context = $request->input('context', []);

        // system role (TMA AI)
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are TMA AI assistant. Extract event information from user input and respond professionally in TMA style.'
            ]
        ];

        // Merge context từ frontend
        if (!empty($context) && is_array($context)) {
            $messages = array_merge($messages, $context);
        }

        // Gọi Gemini API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'),
        ])->post('https://generativelanguage.googleapis.com/v1beta/openai/chat/completions', [
            'model' => 'gemini-2.5-flash',
            'messages' => $messages,
        ]);

        $json = $response->json();

        $answer = $json['choices'][0]['message']['content'] ?? ($json['error']['message'] ?? 'Lỗi khi gọi Gemini API');

        return response()->json([
            'ai_text' => $answer,
        ]);
    }
}
