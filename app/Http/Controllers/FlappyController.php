<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FlappyController extends Controller
{
    public function home(){
        return view('home');
    }
}
