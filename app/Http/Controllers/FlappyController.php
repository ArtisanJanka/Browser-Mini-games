<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlappyLeaderboard;
use Illuminate\Support\Facades\Auth;


class FlappyController extends Controller
{
    public function home(){
        return view('home');
    }

    public function flappybird(){
        return view('flappybird');
    }
    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0',
        ]);

        FlappyLeaderboard::create([
            'user_id' => Auth::id(),
            'score' => $request->score,
        ]);

        return redirect('/flappy-leaderboard');
    }
    public function leaderboard()
    {
        // Step 1: Retrieve all entries with user info, ordered by highest score
        $scores = FlappyLeaderboard::with('user')
            ->orderByDesc('score')               
            ->get();

        // Step 2: Paginate the results (10 per page)
        $paginatedScores = new \Illuminate\Pagination\LengthAwarePaginator(
            $scores->forPage(request()->page ?? 1, 10),
            $scores->count(),
            10,
            request()->page ?? 1,
            ['path' => url()->current()]
        );

        // Step 3: Return to leaderboard view
        return view('flappy_leaderboard', [
            'scores' => $paginatedScores
        ]);
    }


}
