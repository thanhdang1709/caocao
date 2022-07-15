<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
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

    public function getUser(Request $request){
        // $users = User::paginate(20);

        $query = User::query();
        $user_search = $request->user_search;
        if ($user_search) {
            $query->where('email', $user_search)->orWhere('address', $user_search);
        }

        $users = $query->paginate(20);

        return view('user.list', compact('users'));
    }

}
