<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextSample;
use App\Models\Leaderboard;

class TypingGameController extends Controller
{
    public function index() {
        return view('gameselect');
    }

    public function play($difficulty) {
        $wordCount = match($difficulty) {
            'easy' => 50,
            'medium' => 100,
            'hardcore' => 300,
            default => 50
        };

        $text = TextSample::inRandomOrder()->firstWhere('word_count', '>=', $wordCount);
        return view('typing', compact('text', 'difficulty'));
    }

    public function submit(Request $request) {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255',
            'difficulty' => 'required|string',
            'completion_time' => 'required|integer',
        ]);

        Leaderboard::create($validated);
        return redirect()->route('leaderboard');
    }

    public function leaderboard() {
        $scores = Leaderboard::orderBy('completion_time')->get()->groupBy('difficulty');
        return view('leaderboard', compact('scores'));
    }
}
