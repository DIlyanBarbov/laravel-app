<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Itstructure\GridView\DataProviders\EloquentDataProvider;

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
        $user = Auth::user();
        $dataProvider = new EloquentDataProvider(User::query()->where(['id' => $user->getAuthIdentifier()]));
        return view('home', ['dataProvider' => $dataProvider]);
    }
}
