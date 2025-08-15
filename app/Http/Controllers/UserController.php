<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;
    public function index(Request $request): JsonResponse
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

        return $this->successResponse($users, 'Users list');
    }
}

