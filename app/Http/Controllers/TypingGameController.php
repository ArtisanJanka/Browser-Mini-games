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

    public function play($difficulty)
    {
        // Define word count ranges for each difficulty
        $ranges = [
            'easy' => [40, 60],
            'medium' => [80, 120],
            'hard' => [130, 170],
            'hardcore' => [250, 350],
        ];
    
        // Default to easy if difficulty not recognized
        [$min, $max] = $ranges[$difficulty] ?? [40, 60];
    
        $text = TextSample::whereBetween('word_count', [$min, $max])
                    ->inRandomOrder()
                    ->first();
    
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
