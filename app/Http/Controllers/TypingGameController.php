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
        // Step 1: Retrieve all leaderboard entries and eagerly load the 'user' relationship
        $scores = Leaderboard::with('user')  // Eager load the 'user' relationship
            ->orderBy('created_at', 'desc')   // Default sorting by creation date (can be adjusted)
            ->get();                          // Get all the records
    
        // Step 2: Calculate the score for each entry
        $scores->transform(function ($entry) {
            $accuracyDecimal = $entry->accuracy / 100;
            $timeInSeconds = $entry->completion_time / 1000;
            $entry->score = round(($entry->WPM * $accuracyDecimal) / ($timeInSeconds ?: 1), 2); // Avoid divide by zero
            return $entry;
        });
    
        // Step 3: Sort the entire collection by score (in descending order)
        $scores = $scores->sortByDesc('score');  // Sort by score in descending order
    
        // Step 4: Paginate the sorted collection
        $scoresPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $scores->forPage(request()->page ?? 1, 10), // Paginate 10 items per page
            $scores->count(),
            10,
            request()->page ?? 1,
            ['path' => url()->current()]  // Keep the pagination path in URL
        );
    
        // Step 5: Group the entries by difficulty
        $groupedScores = $scoresPaginated->getCollection()->groupBy('difficulty');
    
        // Step 6: Return the paginated and grouped data to the view
        return view('leaderboard', compact('groupedScores', 'scoresPaginated'));
    }
    
    
    
    
    
}
