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
            'Authorization' => 'Bearer ' . env('GEMINI_API_KEY'), //AIzaSyBnFFt6afUgYv2j_1qj0tghF43CK0ZbYtw

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
            'user_text' => $userText,
            'answer'    => $answer,
        ]);
    }
}
