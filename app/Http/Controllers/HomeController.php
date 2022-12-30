<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {
            User::create([
                'name' => $request->input(['name']),
                'email' => $request->input(['email']),
                'password' => Hash::make($request->input(['password'])),
            ]);
        });

        return redirect()->route('home');
    }
}
