<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatBotRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainChatController extends Controller
{
    public function chat(ChatBotRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $message = $request->input('message');

            // Placeholder - cần OpenAI or local LLM
            $response = "Chat về BDS: {$message}. Cần config OpenAI key.";

            return response()->json(['status' => true, 'data' => ['reply' => $response]]);
        } else {
            return response()->json(['status' => false, 'message' => "Có lỗi xảy ra"]);
        }
    }
}
