<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TextSample;
use App\Models\Leaderboard;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
    
    

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'difficulty' => 'required|string',
            'WPM' => 'required|integer',
            'accuracy' => 'required|integer',
            'completion_time' => 'required|integer',
        ]);
    
        // Add authenticated user_id
        $validated['user_id'] = Auth::id();
    
        Leaderboard::create($validated);
    
        return back();
    }
    

    public function leaderboard()
    {
        $scores = Leaderboard::with('user')
            ->get()
            ->groupBy('difficulty')
            ->map(function ($group) {
                return $group->map(function ($entry) {
                    $accuracyDecimal = $entry->accuracy / 100;
                    $timeInSeconds = $entry->completion_time / 1000;
                    $entry->score = round(($entry->WPM * $accuracyDecimal) / ($timeInSeconds ?: 1), 2); // avoid divide by zero
                    return $entry;
                })->sortByDesc('score')->values();
            });
    
        return view('leaderboard', compact('scores'));
    }
    
    
}
