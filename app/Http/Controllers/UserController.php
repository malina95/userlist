<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use function Termwind\render;

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('position')->paginate(6);

        return view('user.index', compact('users'));
    }

    public function more()
    {

    }

}
