<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users except me, plus unread counts
    public function index(Request $request)
    {
        $me = $request->user()->id;

        $users = User::query()
            ->where('id', '!=', $me)
            ->select('id','name','email')
            ->withCount(['sentMessages as unread_count' => function ($q) use ($me) {
                $q->whereNull('read_at')->where('receiver_id', $me);
            }])
            ->orderBy('name')
            ->get();

        return $users;
    }
}

