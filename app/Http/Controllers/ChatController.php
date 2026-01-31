<?php

namespace App\Http\Controllers;

use App\Services\RagChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function ask(Request $request, RagChatService $rag)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $answer = $rag->ask($request->message);

        return response()->json([
            'answer' => $answer,
        ]);
    }
}
