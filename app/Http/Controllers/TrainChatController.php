<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrainChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->message;

        // Placeholder - cần OpenAI or local LLM
        $response = "Chat về BDS: {$message}. Cần config OpenAI key.";

        return response()->json(['reply' => $response]);
    }
}

